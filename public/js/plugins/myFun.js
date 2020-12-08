var postId = 0;
var postBodyElement = null;
var postTitleElement = null;

document.addEventListener('DOMContentLoaded', function () {
    $('.post').find('.interaction').find('.edit').on('click', function (event) {
        event.preventDefault();
    
        postBodyElement = event.target.parentNode.parentNode.childNodes[1];
        var postBody = postBodyElement.textContent;
        postId = event.target.parentNode.parentNode.dataset['postid'];
        postTitleElement = event.target.parentNode.parentNode.parentNode.childNodes[1];
        var posTitle = postTitleElement.textContent;
        $('#post-title').val(posTitle);
        $('#post-body').val(postBody);
        $('#edit-modal').modal();
    });               
});


document.addEventListener('DOMContentLoaded', function () {
    $('#modal-save').on('click', function () {
        console.log(postId);
        console.log(token);
        console.log($('#post-title').val());
        console.log($('#post-body').val());
        $.ajax({
                method: 'POST',
                url: urlEdit,
                data: {title: $('#post-title').val(), description: $('#post-body').val(), postId:postId, _token: token}
            })
            .done(function (msg) { //when ajax request done successfuly
                $(postBodyElement).text($('#post-body').val());//return from the postController
                console.log($('#post-body').val());
                $('#edit-modal').modal('hide');
            });
    });            
});

document.addEventListener('DOMContentLoaded', function () {
    $('.like').on('click', function(event) {
        event.preventDefault();
        postId = event.target.parentNode.parentNode.dataset['postid'];
        var isLike = event.target.previousElementSibling == null;
        console.log(urlLike);
        $.ajax({
            method: 'POST',
            url: urlLike,
            data: {isLike: isLike, postId: postId, _token: token},
            error: function(jqXHR, textStatus, errorThrown) { 
                /* implementation goes here */ 
                console.log(errorThrown);
            }
        }).done(function() {
                event.target.innerText = isLike ? event.target.innerText == 'Like' ? 'You like this post' : 'Like' : event.target.innerText == 'Dislike' ? 'You don\'t like this post' : 'Dislike';
                if (isLike) { //no previousElementSibling and isLike==null
                    console.log('like');
                    event.target.nextElementSibling.innerText = 'Dislike';
                } else {
                    console.log('dislike');
                    event.target.previousElementSibling.innerText = 'Like';
                }
        });
    });          
});

