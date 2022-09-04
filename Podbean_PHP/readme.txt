# Step 1
I. AUTHENTICATION & SAVE ACCESS TOKEN & REFRESH TOKEN 
(RUN ONLY ONCE)
1. Sign up account https://www.podbean.com/site/user/register
2. Create New Podbean App at: https://developers.podbean.com/app/list/
3. Run Podbean_PHP/index.php to copy "Redirect URL" 
4. Paste  "Redirect URI" to (Development) & Redirect URI (Production) of Podbean App (on step 3)
for example: Redirect URL = https://mysite.com/Podbean_PHP/2_podbean_auth_redirect.php
5. Copy & pasts configs: to Podbean_PHP/Podbean/MyPodbeanAuth.php
    + $client_id ="***"
    + $client_secret ="***"
    + $redirect_uri ="Redirect URL"
6. Run Podbean_PHP/1_podbean_auth.php 
7. Confirm action
8. Podbean redirect to "Redirect URL" after confirming & Code save your access_token & refresh_token to file
# Step 2
II. Create, Update & Delete Episode
Read crud_episode.php for examples
