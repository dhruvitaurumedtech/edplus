//role page
document.querySelectorAll('.role_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var roleId = this.getAttribute('data-role-id');

        axios.post('/roles/edit', {
            roleId: roleId
        })
            .then(response => {
                var reponse_data = response.data.roles;
                $('#role_id').val(reponse_data.id);
                $('#role_name').val(reponse_data.role_name);
                $('#exampleModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.role_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var roleId = this.getAttribute('data-role-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/roles/delete', {
                    roleId: roleId
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});

$(document).ready(function () {
    $('#check-all-add').click(function () {
        var isChecked = $(this).prop('checked');
        $('.permission-checkbox-add').prop('checked', isChecked);
    });
    $('#check-all-edit').click(function () {
        var isChecked = $(this).prop('checked');
        $('.permission-checkbox-edit').prop('checked', isChecked);
    });
    $('#check-all-view').click(function () {
        var isChecked = $(this).prop('checked');
        $('.permission-checkbox-view').prop('checked', isChecked);
    });
    $('#check-all-delete').click(function () {
        var isChecked = $(this).prop('checked');
        $('.permission-checkbox-delete').prop('checked', isChecked);
    });
});

//admin page 
document.querySelectorAll('.admin_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var user_id = this.getAttribute('data-user-id');

        axios.post('/admin/edit', {
            user_id: user_id
        })
            .then(response => {
                var reponse_data = response.data.userDT;

                $('#user_id').val(reponse_data.id);
                $('#role_type').val(reponse_data.role_type);
                $('#firstname').val(reponse_data.firstname);
                $('#lastname').val(reponse_data.lastname);
                $('#email').val(reponse_data.email);
                $('#mobile').val(reponse_data.mobile);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.admin_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var user_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/admin/delete', {
                    user_id: user_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});

document.querySelectorAll('.institute_admin_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var user_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');
        axios.post(baseUrl + '/admin/edit', {
            user_id: user_id
        })
            .then(response => {
                var reponse_data = response.data.userDT;

                $('#user_id').val(reponse_data.id);
                $('#role_type').val(reponse_data.role_type);
                $('#firstname').val(reponse_data.firstname);
                $('#lastname').val(reponse_data.lastname);
                $('#email').val(reponse_data.email);
                $('#mobile').val(reponse_data.mobile);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.institute_admin_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var user_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/admin/delete', {
                    user_id: user_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
document.querySelectorAll('.institute_list_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var user_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');
        axios.post(baseUrl + '/institute_list_admin/edit', {
            user_id: user_id
        })
            .then(response => {
                var reponse_data = response.data.userDT;
                console.log(reponse_data);
                $('#user_id').val(reponse_data.id);
                $('#name').val(reponse_data.institute_name);
                $('#email').val(reponse_data.email);
                $('#mobile').val(reponse_data.contact_no);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.institute_list_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var user_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/admin/delete', {
                    user_id: user_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});

//student code 
document.querySelectorAll('.student_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var student_id = this.getAttribute('data-student-id');

        var institute_id = this.getAttribute('data-institute-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');

        axios.post('/student/edit', {
            student_id: student_id,
            institute_id: institute_id
        })
            .then(response => {

                var reponse_student = response.data.studentDT;
                var reponse_studentdetail = response.data.studentsdetailsDT;
                if (reponse_student !== null) {
                    // var imgsrc = 'http://127.0.0.1:8000/' + reponse_student.image;
                    var imgsrc = baseUrl + reponse_student.image;


                    $('#student_id').val(reponse_student.id);
                    $('#firstname').val(reponse_student.firstname);
                    $('#lastname').val(reponse_student.lastname);
                    $('#email').val(reponse_student.email);
                    $('#mobile').val(reponse_student.mobile);
                    $('#address').val(reponse_student.address);
                    $('#dob').val(reponse_student.dob);
                    $('#image').attr('src', imgsrc);
                    $('#uploded_image').val(reponse_student.image);
                }
                if (reponse_studentdetail !== null) {
                    $('#inst_id').val(reponse_studentdetail.institute_id);
                    $('#status').val(reponse_studentdetail.status);
                    $('#Student_detail_id').val(reponse_studentdetail.id);
                    $('#institute_for_id').val(reponse_studentdetail.institute_for_id);
                    $('#board_id').val(reponse_studentdetail.board_id);
                    $('#medium_id').val(reponse_studentdetail.medium_id);
                    $('#class_id').val(reponse_studentdetail.class_id);
                    $('#stream_id').val(reponse_studentdetail.stream_id);
                    // $('#subject_id').val(reponse_studentdetail.subject_id);
                    if (reponse_studentdetail.subject_id != null) {
                        var subjects = reponse_studentdetail.subject_id;
                        var arr_subjects = subjects.split(",");
                        $('#subject_id').val(arr_subjects);
                    }
                }
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});

document.querySelectorAll('.student_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var student_id = this.getAttribute('data-student-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/student/delete', {
                    student_id: student_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});

//institute_for
document.querySelectorAll('.institute_for_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var institute_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');

        axios.post(baseUrl + '/institute-for/edit', {
            institute_id: institute_id
        })
            .then(response => {
                var reponse_data = response.data.Institute_for_model;
                var iconSrc = baseUrl + '/' + reponse_data.icon;

                $('#institute_id').val(reponse_data.id);
                $('#name').val(reponse_data.name);
                $('#icon_update').attr('src', iconSrc);
                $('#old_icon').val(reponse_data.icon);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});

document.querySelectorAll('.institute_for_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        var institute_id = this.getAttribute('data-user-id');

        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/institute-for/delete', {
                    institute_id: institute_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});

//board

document.querySelectorAll('.board_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var board_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');


        axios.post(baseUrl + '/board-edit', {
            board_id: board_id
        })
            .then(response => {

                var reponse_data = response.data.board_list;
                var iconSrc = baseUrl + '/' + reponse_data.icon;
                $('#board_id').val(reponse_data.id);
                $('#old_icon').val(reponse_data.icon);

                $('#name').val(reponse_data.name);
                $('#icon_update').attr('src', iconSrc);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.board_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var board_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('board-delete', {
                    board_id: board_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//class

document.querySelectorAll('.class_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var class_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');

        axios.post(baseUrl + '/class-list/edit', {
            class_id: class_id
        })
            .then(response => {
                var reponse_data = response.data.class_list;
                var iconSrc = baseUrl + '/' + reponse_data.icon;


                $('#class_id').val(reponse_data.id);
                $('#old_icon').val(reponse_data.icon);
                $('#icon_update').attr('src', iconSrc);
                $('#name').val(reponse_data.name);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.class_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var class_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/class/delete', {
                    class_id: class_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//medium 
document.querySelectorAll('.medium_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var medium_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');


        axios.post(baseUrl + '/medium/edit', {
            medium_id: medium_id,
        }, {
        })
            .then(response => {
                var response_data = response.data.medium_list;
                var iconSrc = baseUrl + '/' + response_data.icon;
                $('#medium_id').val(response_data.id);
                $('#name').val(response_data.name);
                $('#old_icon').val(response_data.icon);
                $('#editicon').attr('src', iconSrc);
                $('#status').val(response_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => { });
    });
});

document.querySelectorAll('.medium_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var medium_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/medium/delete', {
                    medium_id: medium_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//standard
document.querySelectorAll('.standard_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var standard_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');

        axios.post(baseUrl + '/standard-list/edit', {
            standard_id: standard_id
        })
            .then(response => {
                var reponse_data = response.data.standard_list;
                $('#standard_id').val(reponse_data.id);
                $('#class_id').val(reponse_data.class_id);
                $('#name').val(reponse_data.name);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.standard_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var standard_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/standard/delete', {
                    standard_id: standard_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//stream

document.querySelectorAll('.stream_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var stream_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');

        axios.post(baseUrl + '/stream-edit', {
            stream_id: stream_id
        })
            .then(response => {
                var reponse_data = response.data.straemlist;
                $('#stream_id').val(reponse_data.id);
                $('#name').val(reponse_data.name);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.stream_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var stream_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('stream-delete', {
                    stream_id: stream_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//do business with 
document.querySelectorAll('.business_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');


        axios.post(baseUrl + '/do-business-with/edit', {
            id: id
        })
            .then(response => {
                var reponse_data = response.data.Dobusinesswith_Model;
                console.log(response);
                $('#id').val(reponse_data.id);
                $('#name').val(reponse_data.name);
                $('#category').val(reponse_data.category_id);
                $('#status').val(reponse_data.status);

                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.business_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/do-business-with/delete', {
                    id: id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
document.querySelectorAll('.chapter_delete').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var id = this.getAttribute('data-user-id');
        
        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('chapter-delete', {
                    id: id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//banner-size
$(document).ready(function () {

    document.querySelectorAll('.banner_size_editButton').forEach(function (button) {
        button.addEventListener('click', function () {
            var bannerId = this.getAttribute('data-banner-id');
            var baseUrl = $('meta[name="base-url"]').attr('content');

            axios.post(baseUrl + '/banner-sizes/edit', {
                banner_id: bannerId
            })
                .then(response => {
                    var response_data = response.data.bannerSize;
                    console.log(response_data.size);
                    $('#id').val(response_data.id);
                    $('#size').val(response_data.size);
                    $('#width').val(response_data.width);
                    $('#height').val(response_data.height);
                    $('#usereditModal').modal('show');
                })
                .catch(error => {
                    console.error(error);
                });
        });
    });
});
document.querySelectorAll('.banner_size_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        var bannerId = this.getAttribute('data-banner-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(baseUrl + '/banner-sizes/destroy', {
                    bannerId: bannerId
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});

//banner
document.querySelectorAll('.banner_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var banner_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');
        axios.post(baseUrl + '/banner/edit', {
            banner_id: banner_id
        })
            .then(response => {

                var reponse_data = response.data.banner_list;
                var iconSrc = baseUrl + '/' + reponse_data.banner_image;

                $('#banner_id').val(reponse_data.id);
                $('#banner_image').attr('src', iconSrc);
                $('#old_banner_image').val(reponse_data.banner_image);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.banner_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var banner_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/banner/delete', {
                    banner_id: banner_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//video category
document.querySelectorAll('.videocategory_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var video_category_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');

        axios.post(baseUrl + '/video-category-edit', {
            video_category_id: video_category_id
        })
            .then(response => {

                var reponse_data = response.data.video_category_list;
                $('#video_category_id').val(reponse_data.id);
                $('#name').val(reponse_data.name);
                $('#status').val(reponse_data.status);
                $('#usereditModal').modal('show');
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.videocategory_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var video_category_id = this.getAttribute('data-user-id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('video-category-delete', {
                    video_category_id: video_category_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//announcement
document.querySelectorAll('.announcement_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var anouncement_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');


        axios.post(baseUrl + '/announcement/edit', {
            anouncement_id: anouncement_id
        })
            .then(response => {
                var response_data = response.data.announcement;
                for (let result of response_data) {

                    var institute_id = result.institute_id;
                    var teacher_id = result.teacher_id;
                    $('#anouncement_id').val(result.id);
                    $('#announcement').val(result.announcement);
                    $('#title').val(result.title);

                    const institute_id_result = institute_id.split(',');
                    for (let institute of institute_id_result) {
                        $(`#institute_id[value="${institute.trim()}"]`).prop('checked', true);
                    }
                    const teacher_id_result = teacher_id.split(',');
                    for (let teacher of teacher_id_result) {
                        $(`#teacher_id[value="${teacher.trim()}"]`).prop('checked', true);
                    }
                    // $('#institute_id').prop('checked', result.institute_id);
                    // $('#teacher_id').prop('checked', result.teacher_id);
                    $('#usereditModal').modal('show');
                }
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.announcement_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var announcement_id = this.getAttribute('data-user-id');
       
        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/announcement/delete', {
                    announcement_id: announcement_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});
//video limit 
document.querySelectorAll('.video_limit_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');
        axios.post(baseUrl + '/video-timelimit-edit', {
            id: id
        })
            .then(response => {
                var response_data = response.data.times;
                for (let result of response_data) {

                    var institute_id = result.institute_id;
                    var teacher_id = result.teacher_id;
                    $('#id').val(result.id);
                    $('#time').val(result.time);
                    const institute_id_result = institute_id.split(',');
                    for (let institute of institute_id_result) {
                        $(`#institute_id[value="${institute.trim()}"]`).prop('checked', true);
                    }
                    const teacher_id_result = teacher_id.split(',');
                    for (let teacher of teacher_id_result) {
                        $(`#teacher_id[value="${teacher.trim()}"]`).prop('checked', true);
                    }
                    $('#usereditModal').modal('show');
                }
            })
            .catch(error => {
                console.error(error);
            });
    });
});
document.querySelectorAll('.video_limit_deletebutton').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var video_time_limit_id = this.getAttribute('data-user-id');
       
        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('video-timelimit-delete', {
                    video_time_limit_id: video_time_limit_id
                })
                    .then(response => {
                        location.reload(true);

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    });
});