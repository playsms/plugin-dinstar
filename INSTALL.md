# Installation

You will need an installed and working playSMS, and assumed your playSMS is installed with these setups:

- Your playSMS web files is in `/var/www/html/playsms`
- Your playSMS database is `playsms`

Follow below steps in order:

1. Clone this repo to your playSMS server

   ```
   cd ~
   git clone https://github.com/playsms/plugin-dinstar.git
   cd plugin-dinstar
   ```

2. Copy gateway source to playSMS `plugin/gateway/`

   ```
   cp -rR src/dinstar /var/www/html/playsms/plugin/gateway/
   ```

3. Restart `playsmsd`

   ```
   playsmsd restart
   playsmsd check
   ```
