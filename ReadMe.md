Website Address Homepage: 

For HTTP 80:
https://s65.ierg4210.ie.cuhk.edu.hk

Normally will redirect to https://secure.s65.ierg4210.ie.cuhk.edu.hk

However, if access directly to https://s65.ierg4210.ie.cuhk.edu.hk
Redirection will stop.

For HTTPS 443:
https://secure.s65.ierg4210.ie.cuhk.edu.hk

-   Home Page: main.php
    -   https://secure.s65.ierg4210.ie.cuhk.edu.hk/main.php

-   Local Environment Required: PHP

-   No framework is used

-   Database: sqlite3

-   External JS Library: JQuery

-   External CSS: Boostrap 

For Security Concerns
Every user actions can only access one corresponding database function:

    - Register => ierg4210_user_register() ONLY
        - can only resgister as normal user
        - Admin type user can only through admin panel to add

    - Change User Password => ierg4210_user_change_pw() ONLY

    - Login => ierg4210_login_verify() ONLY

