# WEB EXPERT TASK

## Run the Project Locally

1. **Clone the repository**

   ```bash
   git clone https://github.com/VarshanidzeAnri/we_task.git
   ```
   ```bash
   cd we_task
   ```
   
2. **Install dependencies**
   ```bash
   composer install
   ```
   
3. **Create and migrate the database**
   ```bash
   php bin/console doctrine:database:create
   ```
   ```bash
   php bin/console make:migration
   ```
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

4. **Run local server**
   ```bash
   composer install
   ```

5. **open this link in browser**
   ```bash
   http://127.0.0.1:8000
   ```

   
