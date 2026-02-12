# OpenCart Backend Practice Project

This repository contains structured backend exercises and feature implementations while learning OpenCart architecture and MVC pattern.

The main focus of this project is understanding how controllers, models, cart logic and business rules work inside a real ecommerce system.

---

## 🎯 Goals of the Project

- Understand OpenCart MVC structure (Controller → Model → View)
- Modify core business logic safely
- Work with cart calculations and pricing logic
- Implement validation rules
- Practice incremental development with Git commits
- Strengthen backend-oriented thinking

---

## 🔧 Implemented Features

### 🛒 Cart & Pricing Logic

- Added 10% discount logic inside `system/library/cart`
- Implemented free gift for orders over $100
- Added minimum order price validation ($20)
- Added VIP logic (customer becomes VIP after total orders > 1000)
- Added special label when buying more than 2 products
- Fixed premium product flag logic in cart
- Added heavy product & premium manufacturer condition check
- Implemented VAT calculation field in Admin Panel
- Prevented product price lower than 1
- Form validation: model length > 5 characters

---

### 👤 Customer & Login Logic

- Added VIP login message
- Restricted certain totals until login
- Show 10% discount message for non-logged users
- Added yearly total orders summary in dashboard
- Added total customer count in footer

---

### 📦 Product & Stock

- Added low stock attention message
- Added image in confirm order menu
- Added promo labels

---

### 🕒 UI Informational Features

- Added working hours & days in footer
- Added date & hour display in footer

---

## 🧠 What I Learned

- How OpenCart routes requests through controllers
- How models retrieve and process data from database
- How cart business logic is structured
- How to extend core functionality carefully
- How to implement business rules inside an ecommerce system
- Importance of validation and conditional pricing
- Using Git incrementally after each completed task

---

## 🚀 How to Use

1. Install a clean OpenCart version.
2. Replace corresponding files with modified ones from this repository.
3. Run locally with XAMPP / LAMP environment.

---

## 📌 Notes

This repository is a learning-focused project where I progressively implemented backend features to better understand OpenCart internal architecture and ecommerce business logic.
