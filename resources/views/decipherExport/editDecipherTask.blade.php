<x-layout>
    <div class="container mt-5">
        <h2 class="text-center">Edit Decipher Auto Export</h2>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('decipherExport.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3 w-50 mx-auto">
                        <label class="form-label">Survey path:</label>
                        <input class="form-control" name="survey_path" placeholder="Please input the survey path..."
                        value="{{ ($taskSettings['server'] ?? '') . '/survey/' . ($taskSettings['survey_path'] ?? '') }}" required/>
                    </div>

                    <div class="page-wrapper">
                        <div class="page-body">
                            <div class="container-xl">
                                <div class="row row-deck row-cards">
                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <label class="form-label">Auto Export name:</label>
                                                <input class="form-control" name="name" placeholder="Type the name of your export..." value="{{ $task->name }}" required/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Formats:</label>
                                                    <select class="form-select" name="format">
                                                        <option value="csv" {{ isset($taskSettings['format']) && $taskSettings['format'] == 'csv' ? 'selected' : '' }}>Excel</option>
                                                        <option value="spss16" {{ isset($taskSettings['format']) && $taskSettings['format'] == 'spss16' ? 'selected' : '' }}>SPSS</option>
                                                        <option value="fwu" {{ isset($taskSettings['format']) && $taskSettings['format'] == 'fwu' ? 'selected' : '' }}>Triple-S</option>
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
                                                        <option value="standard" {{ isset($taskSettings['layout']) && $taskSettings['layout'] == 'standard' ? 'selected' : '' }}>Standard</option>
                                                        <option value="oe_data" {{ isset($taskSettings['layout']) && $taskSettings['layout'] == 'oe_data' ? 'selected' : '' }}>OE data</option>
                                                        <option value="ce_data" {{ isset($taskSettings['layout']) && $taskSettings['layout'] == 'ce_data' ? 'selected' : '' }}>CE data</option>
                                                        <option value="custom_format" {{ isset($taskSettings['layout']) && $taskSettings['layout'] == 'custom_format' ? 'selected' : '' }}>Custom format</option>
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
                                                            <input class="form-check-input" type="radio" name="condition" value="qualified" {{ isset($taskSettings['condition']) && $taskSettings['condition'] == 'qualified' ? 'checked' : '' }} />
                                                            <span class="form-check-label">Qualified respondents</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="condition" value="disqualified" {{ isset($taskSettings['condition']) && $taskSettings['condition'] == 'disqualified' ? 'checked' : '' }} />
                                                            <span class="form-check-label">Disqualified respondents</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="condition" value="all" {{ isset($taskSettings['condition']) && $taskSettings['condition'] == 'all' ? 'checked' : '' }} />
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
                                                    <textarea class="form-control" name="emails" placeholder="JonJones@gmail.com, AlexPereira@kantar.com, ..." required>{{ isset($taskSettings['emails']) ? $taskSettings['emails'] : '' }}</textarea>
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
                                                        <option value="* * * * *" {{ $task->repeat == '* * * * *' ? 'selected' : '' }}>Every minute</option>
                                                        <option value="0 9,21 * * *" {{ $task->repeat == '0 9,21 * * *' ? 'selected' : '' }}>Every day at 9am and 9pm (GMT)</option>
                                                        <option value="0 9 * * *" {{ $task->repeat == '0 9 * * *' ? 'selected' : '' }}>Every day at 9am (GMT)</option>
                                                        <option value="0 9 */3 * *" {{ $task->repeat == '0 9 */3 * *' ? 'selected' : '' }}>Every 3rd day at 9am (GMT)</option>
                                                        <option value="0 9 * * 1" {{ $task->repeat == '0 9 * * 1' ? 'selected' : '' }}>Every Monday at 9am (GMT)</option>                                                        
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
                                                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="{{ $task->start_date->format('Y-m-d\TH:i') }}" required />
                                                    </div>
                                                    <label for="end_date" class="form-label">End time:</label>
                                                    <div class="input-group">
                                                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="{{ $task->end_date->format('Y-m-d\TH:i') }}" required />
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
                        <a href="{{ route('decipherExport.view', $task) }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update the Decipher Auto Export</button>
                    </div>
                </form>
            </div> <!-- Card body -->
        </div> <!-- Card -->
    </div>
</x-layout> 