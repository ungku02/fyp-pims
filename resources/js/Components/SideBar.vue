
<template>
   <!-- <div class="container-fluid">
    <div class="row"> -->
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <div class="d-flex flex-column">
                <!-- <h4 class="ps-3">Nav</h4> -->
                <nav class="nav flex-column ps-3">
                    <a class="nav-link" href="#">Dashboards</a>
                    <a class="nav-link active" href="#">Projects</a>
                    <a class="nav-link" href="#">Task Management</a>
                    <a class="nav-link" href="#">Kanban View</a>
                    <a class="nav-link" href="#">Teams</a>
                    <a class="nav-link" href="#">Integration</a>
                    <a class="nav-link" href="#">Reports/Analytics</a>
                    <a class="nav-link" href="#">Archived</a>
                    <a class="nav-link" href="#">Activity Feed</a>
                    <a class="nav-link" href="#">Setting</a>
                </nav>
            </div>
        </div>
</template>

<style>

.sidebar {
            background-color: #f8f9fa;
            height: 100vh;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: #6c757d;
            font-size: 14px;
        }

     

      .nav-link.active{
        background-color: #6c757d; /* Active state background color */
    color: #fff; /* Active state text color */
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2); /* Optional inset shadow for pressed effect */
      }

      .nav-link:hover {
    background-color: #5a6268; /* Slightly darker gray on hover */
    color: #fff; /* Ensure text stays white on hover */
}

</style>

   