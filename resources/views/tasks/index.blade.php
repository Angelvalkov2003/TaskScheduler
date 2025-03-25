<x-layout>
    @auth
    <div class="container mt-5">
        <h2 class="text-center">Dashboard</h2>
        <div class="d-flex justify-content-between mb-3">
            <h4>Tasks:</h4>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    New Task
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('decipherExport.createDecipherTask') }}">Decipher Auto Export</a></li>
                    <li><a class="dropdown-item" href="#">Confirmit Auto Export</a></li>
                    <li><a class="dropdown-item" href="#">Task 3</a></li>
                </ul>
            </div>
        </div>

        
        <!--<input type="text" class="form-control" placeholder="Search by name or id…" /> -->
        <!-- Таблица със задачи -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th>Status</th>
                                <th>Created by</th>
                                <th> </th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Decipher Auto Export</td>
                                <td>Disney Standart Excel AutoExport</td>
                                <td>12.03.2025</td>
                                <td>20.03.2025</td>
                                <td><span class="status status-green">Active</span></td>
                                <td>Angel Valkov</td>
                                <td><a href="./ViewDecipherAutoExport.html"><button class="btn btn-primary btn-sm">View details</button></a>
                                <a href="#"><button class="btn btn-primary btn-sm">Force</button></a>
                            </td>
                                
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Decipher Auto Export</td>
                                <td>Amazon NPS 912312312 Standart SPSS AutoExport</td>
                                <td>12.03.2025</td>
                                <td>20.03.2025</td>
                                <td><span class="status">Inactive</span></td>
                                <td>Nikolay Mihaylov</td>
                                <td><a href="./ViewDecipherAutoExport.html"><button class="btn btn-primary btn-sm">View details</button></a>
                                    <a href="#"><button class="btn btn-primary btn-sm">Force</button></a></td>
                            </tr>
                            <tr>
                                <td>331</td>
                                <td>Decipher Auto Export</td>
                                <td>Google Surveys Monthly Export</td>
                                <td>12.03.2025</td>
                                <td>20.03.2025</td>
                                <td><span class="status status-green">Active</span></td>
                                <td>Petar Georgiev</td>
                                <td><a href="./ViewDecipherAutoExport.html"><button class="btn btn-primary btn-sm">View details</button></a>
                                    <a href="#"><button class="btn btn-primary btn-sm">Force</button></a></td>
                            </tr>
                            <tr>
                                <td>442</td>
                                <td>Confirmit Auto Export</td>
                                <td>Facebook Ads Performance Report</td>
                                <td>12.03.2025</td>
                                <td>20.03.2025</td>
                                <td><span class="status status-yellow">Archived</span></td>
                                <td>Kristina Ivanova</td>
                                <td><a href="./ViewDecipherAutoExport.html"><button class="btn btn-primary btn-sm">View details</button></a>
                                    <a href="#"><button class="btn btn-primary btn-sm">Force</button></a></td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
        @endauth
</x-layout>