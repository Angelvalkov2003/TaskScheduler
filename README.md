Documentation Task Scheduler 

 

Summary 

This is a system designed to help users manage automated tasks. Upon logging into the platform, an account will be assigned to specific teams, as determined by the admin panel, granting access to perform operations on highly complex tasks. 

Our first task type is Decipher Auto Export, which allows users to automate data delivery to selected email recipients. After providing an API key, users can configure the export with a chosen layout, format, conditions, and schedule. 

The application is designed to be highly scalable and will support additional task types in the future. 

Data Model 

Each user is associated with a team and a role, with the restriction that they can have only one role per team. Additionally, the role is linked to permissions, which define what rights and privileges the user has within a specific team. 

Furthermore, each user is connected to keys, where all necessary credentials are stored, such as the API key for Decipher and other similar keys that may be added in future development. 

Every user can create tasks and has full control over their own tasks. However, their role within the team determines what privileges they have concerning other tasks within that team. Each task can be associated with multiple teams. 

Every task holds its basic attributes, as seen in the diagram, and is linked to Task Settings, where all additional information related to the task is stored. For example, if it is a Decipher task, it would include its Format, Layout, Conditions, and so on. 

Additionally, each task is linked to Task Logs, where all task executions are recorded over time until a new execution occurs. 

The data is sent by email for security reasons, to ensure it cannot be stolen. Two emails are sent: the first with a link and the second with a password. When used, the file is automatically downloaded in the selected format.
 

 

Views 

Navbar: 

Our navbar contains the name of our system, "Task Scheduler," which acts as a button linking to the Dashboard view. Additionally, we have a theme switch button that allows users to toggle between light and dark mode. 

Furthermore, the navbar includes a dropdown menu that displays the currently logged-in account, along with buttons to view account details and log out. 

 

Dashboard: 

Upon successfully logging into the platform, the user will see the Dashboard section, which displays every project they have view access to. Each project is listed with its ID, type, name, start date, end date, status, creator, and options to view task details or execute the task manually once. 

At the top right of the dashboard table, there is a "Create New Task" button. This button opens a dropdown menu, allowing the user to select from all available task types. In the MVP version, only Decipher Auto-Export will be available. 

Additionally, a User Settings button will be accessible in the navbar. 

 

 

 

View Auto Export Decipher: 

The View Decipher Auto-Export Page displays all fields of a Decipher auto-export, starting with its ID, type, creator, status (Active, Deactivated, Archived), start date, end date, repeat schedule, associated team, format, layout, condition, participants, and a link to the project. 

Below this information, users can find options to switch status of task, force the task to execute, edit the task, and delete the task. 

 

 

New Auto Export Decipher: 


If there is no API key, the user is redirected to a page to enter the Decipher API key. 

If an API key is already available, the New Auto Export Decipher page is loaded, containing the following: 

The system checks whether the project is a Decipher project based on the first part of the link. 

It extracts the exact project path. 

It ignores additional URL parameters. 

Link validation is performed. 

Livewire Error Handling: 

If validation fails, the system may return the following errors: 

"No such link exists" 

"No such portal exists" 

"You don’t have permissions for this project" 

Configuration Options After Successful Validation: 

Format – Possible values: Excel, SPSS, Triple-S, TabDelimited, JSON. 

Layout – All available layouts are retrieved via API, and the user selects which one to use. 

Conditions – The user selects a question from the survey (retrieved via API) and can configure: 

Condition: Equal or Not Equal 

Available responses: Checkbox for selecting one or multiple answers 

Logical operators: AND / OR for adding additional conditions(not in the MVP) 

Emails – A field for entering email addresses where the data will be sent. 

Repeat Time – Possible values: every day in 9am and 9pm (GMT), every day 9am (GMT), every 3th day 9am (GMT), every Monday 9:00 am, open field for CRON prompt. 

Start Date & End Date – Specifies the period for data export. 

Confirm Button – Once confirmed, the exported data will be sent to the specified email addresses. 

 

 

Profile Settings 

When we open "Settings" in the navbar dropdown, it navigates us to the "User Profile" view. There, we can see the email of the logged-in account, all the teams they are part of along with their respective roles, and all the keys they possess, such as the Decipher API key and its corresponding value. 

 

 

Tech Stack 

 

Backend Technologies 

Laravel 12 

The application is built using Laravel 12, a PHP framework known for its elegant syntax and robust features. Laravel provides a strong foundation for authentication, routing, database management, and API integration. 

Decipher API Integration 

We use the Forsta Surveys REST API for interacting with survey data. The official documentation can be found at: 

https://forstasurveys.zendesk.com/hc/en-us/articles/4409469957531-Forsta-Surveys-REST-API 

https://docs.developer.focusvision.com/docs/decipher/api#tag/Panel-Data 

Our integration with Decipher allows us to extract and manage survey panel data efficiently. 

 

Frontend Technologies 

Blade Views 

For rendering the user interface, we use Blade, Laravel's templating engine. Blade provides simple yet powerful features such as template inheritance, control structures, and component-based rendering. 

Tabler Admin Template 

We use the Tabler admin template for UI design and responsiveness. Tabler provides modern, clean, and customizable components, ensuring a user-friendly experience. 

Tabler Admin Template 

Laravel Livewire 

Laravel Livewire is utilized for building dynamic, reactive components without requiring JavaScript frameworks. We use this fir the link validation in Decipher Auto Export. 

Laravel Livewire 

 

Validation & Security 

Form Request Validation 

We ensure data integrity using Laravel’s Form Request Validation to enforce validation rules before processing input. 

Form Request Validation Docs 

Role & Permission Management 

User roles and permissions are managed using Spatie Laravel Permission, which allows fine-grained access control. 

Spatie Laravel Permission 

Enum Handling 

We use Laravel Enums for handling predefined values in a structured manner. 

Laravel Enums Guide 

 

Code Quality & Logging 

Laravel Pint 

For code styling and formatting, we use Laravel Pint, which enforces clean and consistent code. 

Laravel Pint 

Activity Logging 

We use Spatie Activity Log to track changes in specific database tables for auditing and debugging. 

Spatie Activity Log 

 

Database Design 

The application's database schema is documented using dbdiagram.io. 

Database Diagram 

The schema includes key tables such as Users, Teams, Tasks, Roles, and API Keys, ensuring a well-structured relational database. 

SQLite 

 
