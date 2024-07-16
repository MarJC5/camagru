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

## Mandatory Part

### Common features

- [x] You will develop a web application. Even if this is not required, try to structure your
application (as a MVC application, for instance).
- [x] Your website should have a decent page layout (meaning at least a header, a main section
and a footer), be able to display correctly on mobile devices and have an adapted layout
on small resolutions.
- [ ] All your forms should have correct validations and the whole site should be secured.
This point is MANDATORY and shall be verified when your application would be eval-
uated. To have an idea, here are some stuff that is NOT considered as SECURE:
  - [x] Store plain or unencrypted passwords in the database.
  - [ ] Offer the ability to inject HTML or “user” JavaScript in badly protected variables.
  - [ ] Offer the ability to upload unwanted content on the server.
  - [ ] Offer the possibility of altering an SQL query.
  - [x] Use an extern form to manipulate so-called private data

### User features

- [x] The application should allow a user to sign up by asking at least a valid email
address, an username and a password with at least a minimum level of complexity.
- [x] At the end of the registration process, an user should confirm his account via a
unique link sent at the email address fullfiled in the registration form.
- [x] The user should then be able to connect to your application, using his username
and his password. He also should be able to tell the application to send a password
reinitialisation mail, if he forget his password.
- [x] The user should be able to disconnect in one click at any time on any page.
- [x] Once connected, an user should modify his username, mail address or password.

### Gallery features

- [x] This part is to be public and must display all the images edited by all the users,
ordered by date of creation. It should also allow (only) a connected user to like
them and/or comment them.
- [x] When an image receives a new comment, the author of the image should be notified
by email. This preference must be set as true by default but can be deactivated in
user’s preferences.
- [x] The list of images must be paginated, with at least 5 elements per page.

### Editing features

- [x] A main section containing the preview of the user’s webcam, the list of superposable
images and a button allowing to capture a picture.
- [x] A side section displaying thumbnails of all previous pictures taken.
- [ ] Superposable images must be selectable and the button allowing to take the pic-
ture should be inactive (not clickable) as long as no superposable image has been
selected.
- [ ] The creation of the final image (so among others the superposing of the two images)
must be done on the server side.
- [ ] Because not everyone has a webcam, you should allow the upload of a user image
instead of capturing one with the webcam.
- [x] The user should be able to delete his edited images, but only his, not other users’
creations.

### Constraints and Mandatory things

Authorized languages:

- [x] [Server] Any (limited to PHP standard library)
- [x] [Client] HTML - CSS - JavaScript (only with browser natives API)

• Authorized frameworks:

- [x] [Server] Any (up to PHP standard library)
- [x] [Client] CSS Frameworks tolerated, unless it adds forbidden JavaScript.

You project should contain imperatively:

- [x] One (or more) container to deploy your site with one command. anything equivalent
to docker-compose is ok.

### Bonus part 2/5

- [x] “AJAXify” exchanges with the server.
- [x] Offer the possibility to a user to share his images on social networks.
- [x] Do an infinite pagination of the gallery part of the site.

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
