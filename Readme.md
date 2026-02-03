<img width="814" height="504" alt="Screenshot 2026-02-03 082442" src="https://github.com/user-attachments/assets/0af49831-bd2b-452d-aa48-2059f9c5db5e" /># Event Registration Module (event_reg)

A custom Drupal 10.x module built as part of the **FOSSEE task**.  
This module provides a complete event registration system with admin configuration, public registration, validation, email notifications, admin listing, filtering, and CSV export — **without using any contrib modules**.

---

## Features

### Admin Features
- Create and manage events
  - Event Name
  - Category (Online Workshop, Hackathon, Conference, One-day Workshop)
  - Event Date
  - Registration Start Date
  - Registration End Date
- Configure email notifications
  - Enable / disable admin notification
  - Set admin email address
- View all registrations
  - Filter by Event Date
  - Filter by Event Name
  - AJAX-based filtering
  - Export registrations as CSV

### Public Features
- Public event registration form
- Registration available only during configured registration window
- AJAX-based dependent dropdowns:
  - Category → Event Date → Event Name
- Form validation
- Email confirmation to user

---

## Technical Constraints (Followed)

- Drupal **10.x** compatible
- **No contrib modules** used
- PSR-4 autoloading
- Dependency Injection used (no `\Drupal::service()` in business logic)
- Drupal Form API
- Drupal Mail API
- Drupal Config API
- Drupal coding standards followed

---

## Installation Steps

### 1. Place the module

### 2. Enable the module

```bash
ddev drush en event_reg -y
ddev drush cr
vendor/bin/drush en event_reg -y
vendor/bin/drush cr 
```

---

### 3. Permissions

Permissions can be managed at:

/admin/people/permissions


Relevant permissions:


Workflow Overview
1. Admin configures events
2. Admin configures email settings
3. User registers for events
4. Validation is applied
5. Data is stored in custom tables
6. Emails are sent to user and admin
7. Admin views and exports registrations



# Event Registration Module

This module provides a complete event registration system with admin-managed events, public user registration, validation, email notifications, reporting, and CSV export.

---

## Permissions

Admin permissions can be managed at:

/admin/people/permissions

### Important Permissions

- Administer event registration  
- View event registrations  

**URL:**  
https://cms-2.0.0.ddev.site/admin/people/permissions

---
<img width="814" height="504" alt="Screenshot 2026-02-03 082442" src="https://github.com/user-attachments/assets/be664a50-efc2-472e-b741-5a5f2054fe13" />


## Workflow & Functionality

### STEP 1: Event Configuration (Admin)

Admins can create and manage events at:

/admin/config/event-reg/events

**URL:**  
https://cms-2.0.0.ddev.site/admin/config/event-reg/events
<img width="1045" height="1067" alt="Screenshot 2026-02-03 082946" src="https://github.com/user-attachments/assets/95344897-e7b2-4663-939b-681ec1b57a3b" />

#### Event Fields

- Event Name  
- Category  
  - Online Workshop  
  - Hackathon  
  - Conference  
  - One-day Workshop  
- Event Date  
- Registration Start Date  
- Registration End Date  

These values control which events are available and when users can register.

---

### STEP 2: Event Registration Settings (Admin)

Admin configuration page:

/admin/config/event-reg/settings

**URL:**  
https://cms-2.0.0.ddev.site/admin/config/event-reg/settings
<img width="754" height="479" alt="Screenshot 2026-02-03 083100" src="https://github.com/user-attachments/assets/59a589ce-c12f-4081-910c-55617884ab7f" />

#### Configuration Options

- Enable / Disable admin email notifications  
- Set admin notification email address  

**Implementation Details**

- Uses Drupal Config API  
- No hard-coded values  

---

### STEP 3: Public Event Registration (User)

Public registration form:

/event/register

**URL:**  
https://cms-2.0.0.ddev.site/event/register
<img width="948" height="1051" alt="Screenshot 2026-02-03 083223" src="https://github.com/user-attachments/assets/7887c055-ff80-41a8-b1b6-e1a76f743478" />

#### Registration Logic

- The form is visible only between the registration start and end dates  
- Dynamic AJAX dropdowns:
  - Category  
  - Event Date (based on selected category)  
  - Event Name (based on selected date)

#### User Fields

- Full Name  
- Email  
- College Name  
- Department  

---

### STEP 4: Validation Rules

Validation is handled using the Drupal Form API.

#### Rules

- Full Name, College, Department  
  - Only letters and spaces allowed  
- Email  
  - Valid email format required  
- Duplicate Registration Prevention  
  - Same email cannot register twice for the same event  

#### Duplicate Logic

email + event_id

---

### STEP 5: Data Storage

All registrations are stored in a custom database table.

#### Database Verification Commands

```bash
ddev drush sqlq "SELECT * FROM event_reg_registration;"
ddev drush sqlq "DESCRIBE event_reg_registration;"
```
### STEP 6: Email Notifications

Emails are sent using the Drupal Mail API.

#### User Email

- Sent after successful registration
- Contains:
  - Name
  - Event Name
  - Category
  - Event Date

#### Admin Email

- Sent only if enabled in settings
- Contains full registration details

Local email testing is done using Mailpit.

**Mailpit URL:**  
https://cms-2.0.0.ddev.site:8026
<img width="1919" height="1148" alt="Screenshot 2026-02-03 083350" src="https://github.com/user-attachments/assets/ab5bafa9-33a6-4c8d-9ac2-8de5abfaf39d" />

---

### STEP 7: Admin Registration Listing

Admins can view all registrations at:

/admin/config/event-reg/registrations
<img width="1843" height="1036" alt="Screenshot 2026-02-03 083552" src="https://github.com/user-attachments/assets/b79ccabf-cab0-45bd-9e02-cf85eca493db" />

(Also available via the admin menu)

#### Features

- List all registrations
- Filter by:
  - Event Date
  - Event Name
- AJAX-based filtering
- Displays total participant count

---

### STEP 8: CSV Export

Admins can export registrations as a CSV file.
<img width="1577" height="355" alt="Screenshot 2026-02-03 083726" src="https://github.com/user-attachments/assets/94e4b1b3-d54e-48c6-b63c-78c3e26dafcf" />

#### Included Fields

- Name
- Email
- College
- Department
- Event Name
- Submission Date

**Implementation Details**
<img width="1864" height="1001" alt="Screenshot 2026-02-03 083850" src="https://github.com/user-attachments/assets/9fd50373-eaaa-4ff6-910e-f931a1a0d697" />

- Uses core PHP CSV handling
- No contributed modules
