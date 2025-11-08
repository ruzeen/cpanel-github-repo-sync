## cPanel & GitHub repo sync

Automatically sync a cPanel repository with a GitHub repository.

In this example, we use a GitHub webhook to pull the latest changes from the repository and update the files in the cPanel account.

### Prerequisites

- A cPanel account with Git Version Control feature enabled.
- A GitHub repository that you want to sync with your cPanel account.

### Steps to Sync cPanel Repository with GitHub Repository

1. **Create a GitHub Repository**: If you don't have a GitHub repository yet, create one and add some files to it.
2. **Set Up Git Version Control in cPanel**:
   - Log in to your cPanel account.
   - Navigate to the "Git Version Control" feature.
   - Click on "Create" to set up a new repository.
   - Choose "Clone a Repository" and enter the URL of your GitHub repository.
   - Provide the path to your desired directory in cPanel where the repository will be cloned (existing empty directory).
   - Click "Create" to clone the repository to your cPanel account.
3. **Set Up a Webhook in GitHub**:
   - Go to your GitHub repository.
   - Click on "Settings" and then "Webhooks".
   - Click "Add webhook".
   - In the "Payload URL" field, enter the URL to a script on your cPanel account that will handle the webhook (you will create this script in the next step).
   - Set the "Content type" to "application/json".
   - Choose "Just the push event".
   - Click "Add webhook".
4. **Create a Webhook Handler Script in cPanel**:

   - In your cPanel account, navigate to the File Manager or use SSH to create a new PHP script (e.g., `webhook.php`) in the root directory of your cloned repository.
   - Add the following code to the script:

   ```php
   <?php
   // webhook.php
   $secret = 'ngvt483tgvfuesflopfd4egr8137ncfevw8hf3t4tv9i34wkvogpeawf8vtfbdhuf8rs5gtyhb2'; // long random string

   $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
   $payload = file_get_contents('php://input');

   $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

   if (!hash_equals($hash, $signature)) {
   http_response_code(403);
   exit('Invalid signature');
   }

   // Run deploy script
   shell_exec('/home/username/public_html/repo/deploy_from_github.sh > /dev/null 2>&1 &');
   http_response_code(200);
   echo 'Deployment triggered';
   ?>
   ```

   - Replace `/home/username/public_html/repo` with the actual path to your cloned repository in cPanel.

5. **Test the Setup**:
   - Make a change in your GitHub repository and push it to the main branch.
   - The webhook should trigger the script in cPanel, pulling the latest changes to your cPanel repository.
6. **Verify the Changes**:
   - Check the files in your cPanel repository to ensure that the changes from GitHub have been successfully pulled.

### Conclusion

You have successfully set up a sync between your cPanel repository and your GitHub repository using webhooks. Now, any changes pushed to the GitHub repository will automatically update the files in your cPanel account. Make sure to test the setup thoroughly to ensure everything works as expected.
