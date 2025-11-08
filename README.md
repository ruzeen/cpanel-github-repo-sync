## cPanel & GitHub repo sync

Automatically sync a cPanel repository with a GitHub repository.

In this example, we use a GitHub webhook to pull the latest changes from the repository and update the files in the cPanel account.

### Prerequisites

- A cPanel account with Git Version Control feature enabled.
- A GitHub repository that you want to sync with your cPanel account.

### Steps to Sync cPanel Repository with GitHub Repository

1. **Create a GitHub Repository**: Clone this repository to your local machine and push it to your GitHub account.
2. **Set Up Git Version Control in cPanel**:
   - Log in to your cPanel account.
   - Navigate to the "Git Version Control" feature.
   - Click on "Create" to set up a new repository.
   - Choose "Clone a Repository" and enter the URL of your GitHub repository.
   - Provide the path to your desired directory in cPanel where the repository will be cloned (existing empty directory).
   - Click "Create" to clone the repository to your cPanel account.
3. **Update .cpanel.yml**:

   - In the `.cpanel.yml` file, update the `DEPLOYPATH` variable to point to your cloned repository in cPanel:

   ```yaml
   - export DEPLOYPATH=/home/username/public_html/repo
   ```

4. **Create deploy_from_github.sh**:

   - Create a file named `deploy_from_github.sh` in your cPanel repository with the following content:

   ```bash
   #!/bin/bash

   # Path to your cPanel git repository
   REPO_PATH=/home/username/public_html/repo
   LOG_FILE=/home/username/public_html/repo/deploy.log

   echo "Running deploy on $(date)" >> $LOG_FILE

   # Step 1: Go to the repo
   cd $REPO_PATH || { echo "Repo path not found!" >> $LOG_FILE; exit 1; }

   # Step 2: Fetch the latest from GitHub
   git fetch origin >> $LOG_FILE 2>&1

   # Step 3: Reset the branch to match GitHub
   git reset --hard origin/master >> $LOG_FILE 2>&1

   # Step 4: Trigger cPanel deployment tasks (.cpanel.yml)
   /usr/local/cpanel/bin/uapi VersionControlDeployment create repository_root=$REPO_PATH >> $LOG_FILE 2>&1
   ```

   - Open your cPanel Terminal, navigate to the directory containing the script, and run this command to make the script executable:

   ```bash
   chmod +x deploy_from_github.sh
   ```

5. **Update webhook.php**:

   - Assign a value to the `$secret` variable. Replace `<your-secret>` with a long random string of your choice.
   - Replace `/home/username/public_html/repo` with the actual path to your cloned repository in cPanel.

6. **Generate SSH Keys**:

   - On your computer (not Cpanel), open a terminal and run the following command to generate SSH keys:

   ```bash
   ssh-keygen -t ed25519 -f github-actions-deploy -C "github-actions-deploy-to-cpanel" -N ""
   ```

   - This creates two files: `github-actions-deploy` (private key) and `github-actions-deploy.pub` (public key).

#### Public Key

- Copy the contents of `github-actions-deploy.pub`.
- Log in to your cPanel account and navigate to "SSH Access" > "Manage SSH Keys" > "Import Key".
- Paste the public key content into the "Public Key" field. Give it a name (eg: `cpanel-github-repo-sync`) and click "Import".
- Next, authorize the key by clicking "Manage" next to the key and then "Authorize".

#### Private Key

- Copy the contents of `github-actions-deploy`.
- In your GitHub repository, go to "Settings" > "Secrets and variables" > "Actions" > "New repository secret".
- Name the secret `CPANEL_SSH_PRIVATE_KEY` and paste the private key content into the "Value" field. Click "Add secret".

7. **Set Up a Webhook in GitHub**:

   - Go to your GitHub repository.
   - Click on "Settings" and then "Webhooks".
   - Click "Add webhook".
   - In the "Payload URL" field, enter the URL to a script on your cPanel account that will handle the webhook (eg: `https://yourdomain.com/repo/webhook.php`).
   - Set the "Content type" to "application/json".
   - Choose "Just the push event".
   - Click "Add webhook".

8. **Test the Setup and Verify the Changes**:
   - Make a change in your GitHub repository and push it to the main branch.
   - The webhook should trigger the script in cPanel, pulling the latest changes to your cPanel repository.
   - Check the files in your cPanel repository to ensure that the changes from GitHub have been successfully pulled.

### Conclusion

You have successfully set up a sync between your cPanel repository and your GitHub repository using webhooks. Now, any changes pushed to the GitHub repository will automatically update the files in your cPanel account. Make sure to test the setup thoroughly to ensure everything works as expected.
