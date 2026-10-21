# 🏛️ Murang'a County Public Service Request System

A web-based public service request and tracking platform built for citizens and administrators in Murang'a County.

---

## 🌟 Key Features

* **Public Request Submission:** Supports categorization across 7 Constituencies, 35 Wards, and specific local Villages with optional image evidence.
* **Anonymous Reporting:** Citizens can submit sensitive concerns while hiding their identity from public officials.
* **Real-Time Status Tracking:** Complete workflow lifecycle: `Pending` → `In Progress` → `Resolved`.
* **Admin Dashboard & Analytics:** High-level case statistics (Open vs. Resolved cases) and inline status updating for administrators.
* **Kenya Data Protection Act 2019 Compliance:** Explicit data privacy consent check on user registration.
* **Accessibility Toolbar:** Dynamic text scaling (A-, A, A+) and high-contrast accessibility mode.
* **Citizen Feedback:** Star-rating and review portal for resolved public requests.

---

## 🛠️ Technology Stack

* **Backend:** PHP 8.x
* **Database:** SQLite 3 (Auto-initializing, light, configuration-free)
* **Frontend:** HTML5, CSS3 (CSS Variables, Flexbox, Grid), JavaScript (Vanilla JS)

## 🚀 Deployment

This application is configured for deployment on **Railway** using the standard PHP / Nixpacks build stack with dynamic port binding (`$PORT`).