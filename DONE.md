# 📝 DONE List

## 🚀 Project Roadmap

### MVP (Minimum Viable Product)

- [x] Set up project repository
  - [x] Initialize a new Git repository
  - [x] Configure README.md, .gitignore, and basic project structure
- [x] Initial project setup
  - [x] Set up Go and Air
  - [x] Set up go lint
  - [x] Set up Dockerfile
  - [x] Set up entrypoint and supervisord
  - [x] Set up releaserc and commitlint
  - [x] Implement hexagonal structure
- [x] Create the project CI
  - [x] Create a CI step to run tests, lint and vet
  - [x] Create a CI step to mark the PR as ready to merge (only on PRs)
  - [x] Create a CI step to run the releaserc (only on main branch push)
  - [x] Create a CI step to build the docker image and push to Docker Hub
  - [x] Create a CI step to run the lint on PR title (to trigger the releaserc and deploy)
  - [x] Create a CI step to deploy the docker image into EC2 server
    - [x] Setup SSH using SSH key
    - [x] Remove old images from EC2 machine
    - [x] Set up the environment variables through CI with docker and github secrets
    - [x] Set up container run
- [x] AWS
  - [x] Integrate S3
- [x] Auth
  - [x] Create the login method
  - [x] Create the register method
  - [x] Create the password forgot method
  - [x] Create the password reset method
  - [x] Create the password reset method from user profile (with current password validation)
  - [x] Implement OAuth for Google and Facebook (Socialite)
- [x] Documentation
  - [x] Create a detailed readme and how to run API locally
  - [x] Create a contribution guide for open source contributors
- [x] Experience for users
  - [x] User should have the experience quantity
- [x] Coins
  - [x] User should have the coins quantity
  - [x] Create user wallet
  - [x] Migrate the user coins quantity for a has one relation for wallet
  - [x] Create missions to earn coins
  - [x] Create daily tasks to earn free coins
- [x] Update user
  - [x] Request password for sensitive changes, such as email or nickname
  - [x] Update user email
  - [x] Update user nickname
  - [x] Update user basics, such as name and birthdate
- [x] Create notifications
  - [x] Notify users for new transaction
  - [x] Get all user notifications
  - [x] Create a notification on service
  - [x] Delete notification
  - [x] Mark notification as read
  - [x] Mark notification as unread
  - [x] Mark all notifications as read
  - [x] Mark all notifications as unread
  - [x] Check if notification belongs to user
  - [x] Delete all user notifications
- [x] Missions system
  - [x] Create missions for the users
  - [x] Add possibility to create missions for specic users
  - [x] Missions can have the possibility to reward with coins and experience
  - [x] Create transaction for coins addition on mission complete
  - [x] Create notification for mission complete
  - [x] Add possibility to earn mission coins plus level up coins when applicable
- [x] Award titles on level pass if applicable
- [x] Award titles on missions complete if applicable
- [x] Title system
  - [x] Create titles requirements
  - [x] Some titles could be earned by doing missions/quests
  - [x] Some titles could be earned just getting some level
- [x] Integrate with Laravel Reverb
  - [x] Create a notification to act with sockets
- [x] Update arward method to deliver user titles on mission complete if applicable
- [x] Update arward method to deliver user titles on level up if applicable
- [x] Buy titles
  - [x] Create method to buy titles
  - [x] Check if user has enough coins
  - [x] Reward user with title
  - [x] Chargeback user amount on wallet if has ocurred any error with process to give title to user (transaction)
  - [x] Create a transaction for this operation
  - [x] Create notification for that operation and transaction
- [x] Create endpoint to toggle enabled title for auth user
  - [x] Disable all other enabled title if given title is different
  - [x] Disable all titles if given title is already enabled
  - [x] Removes user cache on title toggle

### Post MVP
