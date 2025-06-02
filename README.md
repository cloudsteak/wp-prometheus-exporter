# WP Prometheus Exporter

**Verzió:** 1.5.1  
**Készítette:** [Cloud Mentor](https://cloudmentor.hu)  
**GitHub:** [https://github.com/cloudsteak/wp-prometheus-exporter](https://github.com/cloudsteak/wp-prometheus-exporter)

## 📌 Mi ez?

A **WP Prometheus Exporter** egy WordPress plugin, amely lehetővé teszi, hogy a weboldalad statisztikai, teljesítmény- és működésadatokat exportálj **Prometheus-kompatibilis** formátumban. Ezek az adatok ezután vizualizálhatók és elemezhetők **Grafana** segítségével.

Ezáltal valós idejű betekintést kaphatsz a WordPress oldalad belső működésébe, teljesítményébe és növekedési trendjeibe.

---

## ⚙️ Technikai előfeltételek

- WordPress 6.7.2 vagy újabb
- PHP 8.1 vagy újabb
- Prometheus telepítve a környezetedben
- (opcionális) Grafana a metrikák vizualizálásához

_Megjegyzés: Redis használata nem szükséges_

---

## 📥 Telepítés

1. Töltsd le a ZIP fájlt.
2. WordPress adminban: **Bővítmények → Új hozzáadása → Bővítmény feltöltése**
3. Aktiváld a `WP Prometheus Exporter` bővítményt.
4. Menj a **Beállítások → WP Prometheus Exporter** oldalra, és állítsd be a kívánt metrikákat.

---

## 🔍 Elérhető metrikák

- Publikált posztok száma (post típus szerint)
- Jóváhagyott kommentek száma
- Felhasználók száma (szerepkör szerint)
- `wp_options` bejegyzések száma
- Lekérdezések száma és ideje
- Kérés hossza
- Plugin és theme frissítések száma
- Email-ek száma
- Hibák száma

---

## 🔗 Példa: Prometheus konfiguráció

```yaml
scrape_configs:
  - job_name: 'wordpress'
    metrics_path: /v1/metrics
    scrape_interval: 15s
    static_configs:
      - targets: ['yourdomain.com']
```

> Ne feledd: Ha TLS-t használsz, szükséges lehet a `scheme: https` opció hozzáadása.

---

## 📊 Példa: Grafana panel

Prometheus lekérdezés pl.:

```promql
rate(wp_post_count{post_type="post"}[5m])
```

vagy

```promql
wp_user_count{role="administrator"}
```

---

## ℹ️ További információk

- **Prometheus:** [https://prometheus.io](https://prometheus.io)  
- **Grafana:** [https://grafana.com](https://grafana.com)  
- **Plugin fejlesztője:** [https://cloudmentor.hu](https://cloudmentor.hu)

---

## 📃 Licensz

MIT
