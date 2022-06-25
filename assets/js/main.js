$(document).ready(function() {
    //параметры url
    var params = window
        .location
        .search
        .replace('?','')
        .split('&')
        .reduce(
            function(p,e){
                var a = e.split('=');
                p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                return p;
            },
            {}
        );

    const showErrorToast = function(text) {
        let toastError = document.getElementById('error-alert')
        $('#error-alert .toast-body').text(text)
        var toast = new bootstrap.Toast(toastError)
        toast.show()
    }

    const showAccessToast = function(text) {
        let toastAccess = document.getElementById('access-alert')
        $('#access-alert .toast-body').text(text)
        var toast = new bootstrap.Toast(toastAccess)
        toast.show()
    }


    //alert для ошибки
    if (params['error'] !== undefined) {
        let toastLive = document.getElementById('error-alert')
        $('#error-alert .toast-body').text(params['error'])
        var toast = new bootstrap.Toast(toastLive)
        toast.show()
    }

    $("#google_book_search_form").on('submit', function (e) {
        e.preventDefault();
    });

    //поиск книг
    var timer;
    var timerInterval = 300;  //таймер на 0.3 сек
    var booksList;
    $("#google_book_search").on('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            start();
        }, timerInterval)
    })

    const start = async function(a, b) {
        var searchStr = document.getElementById("google_book_search").value;
        var url = 'https://www.googleapis.com/books/v1/volumes?q=' + searchStr;

        let response = await fetch(url);
        if (response.ok) {
            let googleBooksResponse = await response.json();
            console.log(googleBooksResponse);
            let books = googleBooksResponse.items;
            clearBookList();
            if (books !== undefined && books.length > 0){
                booksList = books;
                showBookList(googleBooksResponse.items);
            }
        } else {
            clearBookList();
        }
    }

    const clearBookList = () => {
        $(".table-search-result tbody").empty();
    }

    const showBookList = (bookList) => {
        for (key in bookList){
            let title = bookList[key].volumeInfo.title;
            if (bookList[key].volumeInfo.subtitle != undefined)
                title += ". "+bookList[key].volumeInfo.subtitle
            key++;
            $(".table-search-result tbody").append(
                "<tr>" +
                    "<th>"+key+".</th>" +
                    "<td>"+ title +"</td>" +
                    "<td style='width: 100px'>"+"<button type='button' class='btn btn-success add_book_modal' data-bs-toggle='modal' data-bs-target='#bookAddModal' data-bookindex='"+key+"'>Add</button>"+"</td>" +
                "</tr>"
            );
        }
    }

    //модалка добавления книги
    $(document).on('click', '.add_book_modal' , function (e) {
        $("#bookAddModal #book_index").val(this.dataset.bookindex);
    })

    //добавление книги
    $(document).on('click', '.add_book' , function (e) {
        let bookModal  = document.getElementById('bookAddModal');
        let modal = bootstrap.Modal.getInstance(bookModal);
        var bookId = document.getElementById("book_index").value;
        let needBook = booksList[bookId-1];
        modal.hide();
        var btn = $('.add_book_modal[data-bookindex="'+bookId+'"]');
        btn.prop('disabled', true);
        btn.removeClass("btn-success ");
        btn.addClass("btn-secondary");
        const objBookToSend = {};
        objBookToSend.uid = needBook.id;
        objBookToSend.title = needBook.volumeInfo.title;
        objBookToSend.authors = needBook.volumeInfo.authors;
        objBookToSend.description = needBook.volumeInfo.description;

        fetch(window.location.origin + "/?c=Library&a=addbook", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(objBookToSend)
        }).then(
            function(response) {
                response.json().then(function(data) {
                    if (data.error){
                        showErrorToast(data.error.message)
                        btn.prop('disabled', false);
                        btn.removeClass("btn-secondary");
                        btn.addClass("btn-success");
                    }
                    if (data.success){
                        showAccessToast(data.success.message)

                    }
                });
            }
        ).catch(function(err) {
            showErrorToast(err);
        })

        console.log('Добавим книгу');
    })


    //добавление в избранное
    $(document).on('click', '.addFavoriteBook' , function (e) {
        console.log('addFavoriteBook')

        var bookFavoriteid = $(this).data('id');
        objBookToSend = {};
        objBookToSend.bookId = bookFavoriteid;
        var btn = $('.addFavoriteBook[data-id="'+bookFavoriteid+'"]');
        btn.prop('disabled', true);
        fetch(window.location.origin + "/?c=Library&a=addFavoriteBook", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(objBookToSend)
        }).then(
            function(response) {
                response.json().then(function(data) {
                    if (data.error){
                        btn.prop('disabled', false);
                        showErrorToast(data.error.message)
                    }
                    if (data.success){
                        showAccessToast(data.success.message)
                        btn.removeClass("btn-success ");
                        btn.addClass("btn-secondary");
                    }
                });
            }
        ).catch(function(err) {
            showErrorToast(err);
        })
    })

    //модалка удаления книги
    $(document).on('click', '.delete_book_modal' , function (e) {
        $("#bookDeleteModal #delete_book_id").val(this.dataset.id);
    })
    //удаление из общего списка
    $(document).on('click', '.delete_book' , function (e) {
        console.log('-1-')
        console.log(this)

        let bookModal  = document.getElementById('bookDeleteModal');
        let modal = bootstrap.Modal.getInstance(bookModal);
        modal.hide();

        var bookId = document.getElementById("delete_book_id").value;
        const objBookToSend = {};
        objBookToSend.bookId = bookId;

        var btn = $('.delete_book_modal[data-id="'+bookId+'"]');
        btn.prop('disabled', true);
        btn.removeClass("btn-danger");
        btn.addClass("btn-secondary");
        fetch(window.location.origin + "/?c=Library&a=deleteBook", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(objBookToSend)
        }).then(
            function(response) {
                response.json().then(function(data) {
                    if (data.error){
                        showErrorToast(data.error.message)
                        btn.prop('disabled', false);
                        btn.removeClass("btn-secondary");
                        btn.addClass("btn-danger");
                    }
                    if (data.success){
                        removeBookLine(bookId)
                        showAccessToast(data.success.message)
                    }
                });
            }
        ).catch(function(err) {
            showErrorToast(err);
        })

        console.log('Добавим книгу');
    })
    
    const removeBookLine = (bookId) => {
        $('.table-list tbody tr#bood-' + bookId).remove();
        updateListTableNum();
    }

    const updateListTableNum = () => {
        $('.table-list tbody tr').each(function(i) {
            $(this).find('td:first').text(i+1);
        });
    }
});