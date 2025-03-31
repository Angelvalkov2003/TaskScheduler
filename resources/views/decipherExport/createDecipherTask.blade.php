<x-layout>
    <div class="container mt-5">
        <h2 class="text-center">Decipher Auto Export Create Page</h2>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('decipherExport.store') }}" method="POST">
                    @csrf

                    <div class="mb-3 w-50 mx-auto">
                        <label class="form-label">Survey path:</label>
                        <input class="form-control" name="survey_path" placeholder="Please input the survey path..." required/>
                    </div>

                    <div class="page-wrapper">
                        <div class="page-body">
                            <div class="container-xl">
                                <div class="row row-deck row-cards">
                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <label class="form-label">Auto Export name:</label>
                                                <input class="form-control" name="name" placeholder="Type the name of your export..." required/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Formats:</label>
                                                    <select class="form-select" name="format">
                                                        <option value="xlsx">Excel</option>
                                                        <option value="spss">SPSS</option>
                                                        <option value="tripleS">Triple-S</option>
                                                        <option value="json">JSON</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Layout:</label>
                                                    <select class="form-select" name="layout">
                                                        <option value="standard">Standard</option>
                                                        <option value="oe_data">OE data</option>
                                                        <option value="ce_data">CE data</option>
                                                        <option value="custom_format">Custom format</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="form-label">Condition:</div>
                                                    <div>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="condition" value="qualified" checked />
                                                            <span class="form-check-label">Qualified respondents</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="condition" value="disqualified" />
                                                            <span class="form-check-label">Disqualified respondents</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="condition" value="all" />
                                                            <span class="form-check-label">All respondents</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Emails to receive:</label>
                                                    <textarea class="form-control" name="emails" placeholder="JohnJones@gmail.com, AlexPereira@kantar.com, ..." required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label" for="repeat">Repeat:</label>
                                                    <select class="form-select" id="repeat" name="repeat">
                                                        <option value="* * * * *">Every minute</option>
                                                        <option value="0 9 * * *">Every day at 9am (GMT)</option>
                                                        <option value="0 9,21 * * *">Every day at 9am and 9pm (GMT)</option>
                                                        <option value="0 9 */3 * *">Every 3rd day at 9am (GMT)</option>
                                                        <option value="0 9 * * 1">Every Monday at 9am (GMT)</option>                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="start_date" class="form-label">Start time:</label>
                                                    <div class="input-group">
                                                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" required />
                                                    </div>
                                                    <label for="end_date" class="form-label">End time:</label>
                                                    <div class="input-group">
                                                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" required />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End row -->
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save the Decipher Auto Export</button>
                    </div>
                </form>
            </div> <!-- Card body -->
        </div> <!-- Card -->
    </div>
</x-layout>
