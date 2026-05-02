# IronLog – Workout Tracking Web Application

## 📌 Overview
IronLog is a functional web-based fitness tracking application designed to allow users to record workouts, monitor progress, and achieve fitness goals over time. The system provides a simple and effective way for gym users to track both strength training and cardio performance in a structured manner.

The application was developed following a full software development lifecycle, including requirements analysis, database design, iterative development, and testing. The final system meets all functional and non-functional requirements defined at the start of the project.

---

## 🚀 Features
- Log workouts including exercises, sets, reps, and weight
- Support for both strength training and cardio (distance tracking)
- View workout history grouped by date
- Track progress using charts and personal records
- Set fitness goals and monitor progress
- Dashboard displaying key statistics and workout streaks

---

## 👤 Use Case
A gym user records each workout session by entering exercises, sets, and weights used. Over time, the system stores this data and presents it through dashboards and progress charts, allowing the user to monitor improvements, maintain consistency, and achieve personal fitness goals.

---

## 🛠️ Technologies Used
- PHP (server-side logic)
- MySQL (database management)
- HTML, CSS (user interface)
- JavaScript (dynamic behaviour and charts)
- XAMPP (local development environment)

---

## ⚙️ System Setup (XAMPP)

### Step 1: Install and Run XAMPP
1. Download XAMPP from: https://www.apachefriends.org  
2. Install using default settings  
3. Open XAMPP Control Panel  
4. Start:
   - Apache  
   - MySQL  

---

### Step 2: Import Database
1. Open browser: http://localhost/phpmyadmin  
2. Click **New** and create a database named:
ironlog
3. Go to **Import** tab  
4. Select the file:
ironlog.sql
5. Click **Go**  

---

### Step 3: Project Setup
1. Navigate to:
- Windows: `C:\xampp\htdocs\`  
- Mac: `/Applications/XAMPP/htdocs/`  
- Linux: `/opt/lampp/htdocs/`  

2. Create a folder named:
ironlog

3. Place all project files inside this folder

---

### Step 4: Run the Application
1. Open browser:
http://localhost/ironlog/
2. Login using:
- Username: `demo`  
- Password: `demo123`  

---

## 📊 Project Structure
ironlog/
├── index.php # Login and entry point
├── dashboard.php # Dashboard with statistics
├── log.php # Workout logging system
├── history.php # Workout history view
├── goals.php # Goal tracking system
├── progress.php # Charts and personal records
├── logout.php # Session termination
├── ironlog.sql # Database schema and sample data
├── config/
│ ├── db.php # Database connection (PDO)
│ ├── auth.php # Session and authentication logic
│ ├── exercises.php # Exercise data
│ ├── header.php # Shared UI components
│ └── footer.php
├── css/
│ └── style.css # Application styling
└── js/
└── script.js # Dynamic behaviour and charts

---

## ⚖️ Evaluation and Limitations
The system successfully meets all functional requirements, with all test cases passing during manual testing. Performance is efficient in a local environment, with fast load times and consistent behaviour across major browsers.

However, the system has some limitations:
- Designed for single-user use on a local environment  
- No load testing for multi-user scenarios  
- Manual testing may introduce bias  
- Limited advanced analytics and automation  

---

## 🔮 Future Improvements
- Automated testing using PHPUnit  
- Progressive Web App (PWA) support  
- REST API for mobile or external integration  
- Data export (CSV/JSON)  
- Workout templates and program builder  
- Multi-user and scalable deployment architecture  

---

## 📄 License
This project was developed as part of a university coursework submission and is intended for educational use.



   
