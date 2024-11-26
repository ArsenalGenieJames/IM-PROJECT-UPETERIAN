document.addEventListener("DOMContentLoaded", function () {
    const commentButtons = document.querySelectorAll('.show-comments');
    commentButtons.forEach(button => {
        button.addEventListener('click', function () {
            const postId = this.getAttribute('data-post-id');
            const commentsDiv = document.getElementById('comments-' + postId);

            if (commentsDiv.style.display === 'none' || commentsDiv.style.display === '') {
                commentsDiv.style.display = 'block';
                this.textContent = 'Hide Comments';
            } else {
                commentsDiv.style.display = 'none';
                this.textContent = 'Show Comments';
            }
        });
    });
});