# Chat Application Backend 🚀

This is a lightweight PHP backend for a chat application, developed as part of the Bunq Backend Engineering Coding Assignment for my application about Backend Enginnering Intern TR. Built using the **Slim Framework** and **Eloquent ORM** with a local **SQLite database**, it supports public chat groups where users can join, send messages, and list messages. No GUI or authentication is included as per the task spec user identity is handled via **user IDs** in the request payload. 🛠️

## Project Structure 📂
The project rocks a clean, organized setup with:
- **PSR-4 autoloading** for tidy code.
- Separation of concerns between **routing**, **controllers**, **models**, and **database setup**.
- **Slim Framework** for RESTful routing.
- **Eloquent ORM** for smooth database interactions.
- **PHPUnit** for full test coverage.

All endpoints are **RESTful** and communicate via **JSON** over **HTTP(s)**. 🌐

## Features ✨
The following features are fully implemented:
- **Create a public chat group**: `POST /groups` 🆕
- **Join a public group**: `POST /groups/{id}/join` 🤝
- **Send a message to a group**: `POST /groups/{id}/messages` 💬
- **List all messages in a group**: `GET /groups/{id}/messages` (supports `?since=timestamp`) 📜
- **List all chat groups**: `GET /groups` 📋

## Database Schema 🗄️
The database consists of four main tables:
- **users**: Stores user info.
- **groups**: Stores group details, including the creator.
- **group_members**: Tracks group memberships.
- **messages**: Stores messages linked to groups and senders.

Data is persisted in a local **SQLite** file for simplicity. 💾

## Testing ✅
Testing is handled with **PHPUnit**, covering all main endpoints. Tests are request-level using Slim’s request factory, and the database is reset before each test for isolation and consistency. Covered scenarios:
- Group creation
- Joining groups
- Sending messages
- Listing messages
- Listing all groups

Run tests with:
```bash
./vendor/bin/phpunit
```

## Setup Instructions 🛠️
1. **Clone the repo**:
   ```bash
   git clone https://github.com/emirrcodes/bunq-case-slim-chat-backend.git
   cd <bunq-case-slim-chat-backend>
   ```
2. **Install dependencies**:
   ```bash
   composer install
   ```
3. **Set up the environment**:
   - Create a `.env` file in the root directory.
   - Add the database path and database driver(e.g., 
   `DB_DRIVER=sqlite
    DB_DATABASE=your-path/bunq-case-slim-chat-backend/database.sqlite`).
4. **Run migrations** to create tables:
   ```bash
   php migrations.php
   ```
5. **Start the server**:
   ```bash
   php -S localhost:8080 -t public
   ```
6. **Interact with the API** using tools like **Postman** or **cURL**. 🧑‍💻

## Notes 📝
- **User Identification** 🔐: As the task allowed using token, username, or ID to identify users, this implementation opts for simple `user_id` values passed in request payloads. This was chosen to maintain minimal complexity and avoid the need for authentication handling, which was explicitly out of scope.
- **HTTP(s) Support** 🌐: While the current implementation is intended for local use and development over HTTP, all communication is JSON over HTTP(s) and can easily be served over HTTPS in production setups.
- **Scalability** ⚙️: This backend is designed as a minimal, modular, and testable application. While it uses SQLite for simplicity, the clean ORM-based architecture allows for scaling up to other SQL databases like PostgreSQL or MySQL with minimal changes.
- The backend is kept minimal, focusing on **clarity**, **code quality**, and **testability**. It’s ready for extensions like user authentication, private groups, or pagination. 🚀
- Prepared for clean delivery and review! 🎯

## Author
**Ahmet Emir Arslan** ([ahmetemirarslan.resmi@gmail.com](mailto:ahmetemirarslan.resmi@gmail.com)) 📧
