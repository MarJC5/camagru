# Camagru

Create a small Instagram-like site allowing users to create and share photo montages. Thus implement, with a bare hands (frameworks are prohibited), the basic functionalities encountered on the majority of sites with a user base.

## Development Plan

Here’s a high-level plan to tackle this project:

1. Setup Development Environment:
    - Configure local development environment with PHP, MySQL (or any SQL database), and basic client-side technologies.
    - Setup Docker for containerization.
2. Backend Development (PHP):
    - Implement core backend features such as user authentication, image handling, and email services.
    - Develop APIs for frontend-backend communication.
    - Ensure security practices are integrated, like input sanitation, CSRF protection, and secure password storage.
3. Frontend Development:
    - Create the user interface with HTML and CSS.
    - Implement interactive elements using JavaScript, particularly for the editing features.
    - Ensure responsiveness and cross-browser compatibility.
4. Testing and Debugging:
    - Conduct thorough testing on both client and server sides.
    - Debug any issues found during testing, ensuring no console errors or warnings.
5. Deployment:
    - Prepare Docker containers and scripts for deployment.
    - Deploy to a test environment to validate functionality in a production-like setting.
6. Documentation and Final Review:
    - Document the application setup and user guide.
    - Review the entire application for any potential improvements or missing requirements.

## Database

### Summary of Relationships

- `users -> medias` (media_id can be NULL, meaning a user may or may not have a profile picture)
- `pages -> medias` (media_id can be NULL, meaning a page may or may not have a media attachment)
- `posts -> users` (user_id references the creator of the post)
- `posts -> medias` (media_id references the media attached to the post)
- `likes -> users` (user_id references the user who liked the post)
- `likes -> posts` (post_id references the liked post)
- `comments -> users` (user_id references the user who made the comment)
- `comments -> posts` (post_id references the post on which the comment was made)
- `medias -> users` (user_id references the owner of the media)
