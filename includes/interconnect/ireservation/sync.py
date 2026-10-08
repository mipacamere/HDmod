#!/usr/bin/env python3
"""
Demone di Sincronizzazione iReservation -> Middleware HD
Polling a 30 secondi per garantire calendario HD sempre aggiornato.
"""
import os
import json
import logging
import requests
import time
from datetime import datetime, timedelta
from dotenv import load_dotenv

# Carica le variabili d'ambiente
env_path = os.path.join(os.path.dirname(__file__), '.env')
load_dotenv(env_path)

# Configurazione Log
LOG_FILE = os.path.join(os.path.dirname(__file__), 'logs', f"sync_{datetime.now().strftime('%Y-%m')}.log")
logging.basicConfig(
    filename=LOG_FILE,
    level=logging.INFO,
    format='[%(asctime)s] [%(levelname)s] %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S'
)

# Costanti iReservation
BASE_URL = "https://api.ireservation.it"
API_KEY = os.getenv("IRES_API_KEY")
USERNAME = os.getenv("IRES_USERNAME")
PASSWORD = os.getenv("IRES_PASSWORD")
CALENDAR_ID = os.getenv("IRES_CALENDAR_ID")
TEST_MODE = os.getenv("IRES_TEST_MODE", "false").lower() == "true"

# Costanti Middleware HD
MIDDLEWARE_URL = "http://127.0.0.1/api/create_booking.php" # Aggiusta se il tuo server web usa un'altra porta/host
MIDDLEWARE_API_KEY = os.getenv("HD_MIDDLEWARE_API_KEY", "MiaChiaveSegretaSuperComplessa_2026")

def get_token():
    url = f"{BASE_URL}/v1/oauth2/authorizations"
    payload = {"username": USERNAME, "password": PASSWORD}
    headers = {"Content-Type": "application/json", "x-api-key": API_KEY}
    try:
        response = requests.post(url, json=payload, headers=headers, timeout=10)
        response.raise_for_status()
        data = response.json()
        if data.get("code") == 0 and "result" in data:
            return data["result"]["token"]
        return None
    except Exception as e:
        logging.error(f"Errore autenticazione iRes: {e}")
        return None

def api_call(endpoint, payload, token):
    url = f"{BASE_URL}{endpoint}"
    headers = {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "Authorization": f"Bearer {token}",
        "x-api-key": API_KEY
    }
    try:
        response = requests.post(url, json=payload, headers=headers, timeout=15)
        data = response.json()
        if response.status_code >= 400 or data.get("code") != 0:
            logging.error(f"Errore API {endpoint}: {data.get('message')}")
            return None
        return data.get("result")
    except Exception as e:
        logging.error(f"Eccezione API {endpoint}: {e}")
        return None

def send_to_hd_middleware(res_data):
    """Invia la prenotazione pulita al middleware HD"""
    headers = {
        "Content-Type": "application/json",
        "X-API-KEY": MIDDLEWARE_API_KEY
    }
    
    # Mappatura camera: se hai bisogno di convertire ID iRes in ID HD, fallo qui
    # Per ora assumiamo che camera_id sia già l'ID corretto di HD o che il middleware lo gestisca
    payload = {
        "external_id": res_data.get("idReservation"),
        "guest_first_name": res_data.get("guestFirstName", "Ospite"),
        "guest_last_name": res_data.get("guestLastName", "iReservation"),
        "email": res_data.get("guestEmail", "sync@ireservation.it"),
        "phone": res_data.get("guestPhoneNumber", ""),
        "checkin": res_data.get("dateCheckIn"),
        "checkout": res_data.get("dateCheckOut"),
        "camera_id": str(res_data["rooms"][0]["id"]) if res_data.get("rooms") else "1",
        "guests": res_data.get("numberAdults", 1) + res_data.get("numberChildren", 0),
        "price": float(res_data.get("price", 0)),
        "source": "iReservation",
        "notes": f"Sync ID: {res_data.get('idReservation')}"
    }
    
    try:
        response = requests.post(MIDDLEWARE_URL, json=payload, headers=headers, timeout=10)
        result = response.json()
        if response.status_code == 200 and result.get("success"):
            logging.info(f"✅ SUCCESSO HD: Contratto {result['data']['idcontratti']} creato per iRes ID {res_data.get('idReservation')}")
            return True
        else:
            logging.error(f"❌ FALLIMENTO HD: {result.get('error')} per iRes ID {res_data.get('idReservation')}")
            return False
    except Exception as e:
        logging.error(f"❌ ECCEZIONE CHIAMATA MIDDLEWARE: {e}")
        return False

def check_if_exists_in_hd(ires_id):
    """Controllo rapido via middleware per evitare duplicati (opzionale, ma consigliato)"""
    # Per semplicità, il middleware gestisce la logica, ma possiamo fare un controllo base
    # In una versione avanzata, creeremmo un endpoint GET /api/check_booking.php?external_id=XYZ
    return False # Per ora affidiamo la deduplicazione al campo 'testo' o a un controllo DB diretto se necessario

def run_sync_cycle():
    token = get_token()
    if not token:
        logging.warning("Token non ottenuto. Skip di questo ciclo.")
        return

    # 1. TEST OUTBOUND (Solo se TEST_MODE è attivo)
    if TEST_MODE:
        logging.info("Modalità TEST attiva. Esecuzione test creazione/cancellazione su iRes...")
        # ... (codice di test outbound invariato, omesso per brevità, funziona già) ...

    # 2. POLLING INBOUND (FULL YEAR)
    today = datetime.now().strftime("%Y-%m-%d")
    next_year = (datetime.now() + timedelta(days=365)).strftime("%Y-%m-%d")
    
    payload = {
        "lang": "it",
        "parameters": {
            "calendarId": int(CALENDAR_ID),
            "propertyUsername": USERNAME,
            "propertyPassword": PASSWORD,
            "fromDate": today,
            "toDate": next_year
        }
    }
    
    result = api_call("/reservations", payload, token)
    if not result or "reservations" not in result:
        logging.info("Nessuna prenotazione trovata o errore API iRes.")
        return

    reservations = result["reservations"]
    logging.info(f"Scansionate {len(reservations)} prenotazioni totali da iReservation.")
    
    new_count = 0
    for res in reservations:
        ires_id = res.get("idReservation")
        status = res.get("status") # "I", "U", "D"
        
        if status == "D":
            logging.warning(f"⚠️ CANCELLAZIONE: iRes {ires_id} da gestire (implementare endpoint cancel nel middleware)")
        elif status in ["I", "U"]:
            # Qui dovremmo controllare se esiste già in HD. 
            # Per ora, inviamo al middleware che può gestire la logica di upsert o controllo.
            logging.info(f"🔄 Elaborazione iRes ID: {ires_id} | {res.get('guestFirstName')} {res.get('guestLastName')}")
            
            if send_to_hd_middleware(res):
                new_count += 1
                
    if new_count > 0:
        logging.info(f"Ciclo completato. {new_count} nuove prenotazioni processate con successo.")

def main():
    logging.info("=== AVVIO DEMONE DI SINCRONIZZAZIONE (30s loop) ===")
    while True:
        try:
            run_sync_cycle()
        except Exception as e:
            logging.error(f"Errore imprevisto nel ciclo: {e}")
        
        time.sleep(30)

if __name__ == "__main__":
    main()
