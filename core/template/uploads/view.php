<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            Post Information
        </div>
        <div class="card-body">
            <h5 class="card-title">Name: {{post.name}}</h5>
            <p class="card-text">Description: {{post.description}}</p>
            <p class="card-text">Author: {{post.author}}</p>
            <p class="card-text">Date: {{post.date}}</p>
            <p class="card-text">Status: {{post.status}}</p>
            <p class="card-text">Category: {{post.category}}</p>
            <hr>
            <h5 class="card-title">Files:</h5>
            <ul class="list-group">
                {{#post.files}}
                <li class="list-group-item">
                    <a href="/uploads/files/download/{{id}}" class="btn btn-link">
                        <!-- Вставьте здесь логотип обозначения файла -->
                        {{file_name}}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

      