<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        <h1>Users List</h1>
        <ul id="user-list">
            @foreach ($users as $user)
                <li>{{ $user->name }} - {{ $user->email }} - {{ $user->photo }}</li>
            @endforeach
        </ul>
        <button id="load-more" class="m-2 btn btn-primary">Show More</button>
        <br>
        <form class="container" action="/users" method="POST" enctype="multipart/form-data">
            @csrf
            <h2>Create new user</h2>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" placeholder="Name"><br>
                <input type="email" class="form-control" name="email" placeholder="Email"><br>
                <input type="password" class="form-control" name="password" placeholder="Password"><br>
                <input type="file" name="photo" accept="image/*"><br>
            </div>
            <button type="submit" class="my-4 btn btn-primary">Add User</button>
        </form>
    </div>

    <script>
        let page = 1;

        document.getElementById('load-more').addEventListener('click', loadMore);

        function loadUsers() {
            fetch(`/users/show-more?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    const userList = document.getElementById('user-list');
                    data.data.forEach(user => {
                        const li = document.createElement('li');
                        li.textContent = `${user.name} - ${user.email} - ${user.photo}`;
                        userList.appendChild(li);
                    });

                    if (data.next_page_url) {
                        page++;
                    } else {
                        document.getElementById('load-more').style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading users:', error));
        }

        function loadMore() {
            page++;
            loadUsers();
        }
    </script>
</body>

</html>
