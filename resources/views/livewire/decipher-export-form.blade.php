<div>
    <form wire:submit="store">
        @csrf

        <div class="mb-3 w-50 mx-auto">
            <label class="form-label">Survey path:</label>
            <div class="input-group">
                <input 
                    class="form-control @error('surveyPath') is-invalid @enderror" 
                    wire:model.live.debounce.500ms="surveyPath" 
                    placeholder="Please input the survey path..." 
                    required
                />
                <span class="input-group-text">
                    <span wire:loading wire:target="surveyPath">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </span>
                    <span wire:loading.remove wire:target="surveyPath">
                        @if($isValidated)
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l5 5l10 -10"></path>
                            </svg>
                        @elseif($errorMessage)
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 9v2m0 4v.01"></path>
                                <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path>
                            </svg>
                        @endif
                    </span>
                </span>
            </div>
            @error('surveyPath') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($errorMessage)
                <div class="text-danger mt-1">{{ $errorMessage }}</div>
            @elseif($successMessage)
                <div class="text-success mt-1">{{ $successMessage }}</div>
            @endif
        </div>

        <!-- API Key Modal -->
        <div class="modal fade" id="apiKeyModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add API Key</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Server:</label>
                            <input type="text" class="form-control" wire:model="serverUrl" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key:</label>
                            <input type="text" class="form-control @error('newApiKey') is-invalid @enderror" wire:model="newApiKey" required>
                            @error('newApiKey') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" wire:click="saveApiKey">Save API Key</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-deck row-cards">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <label class="form-label">Auto Export name:</label>
                                    <input class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Type the name of your export..." required/>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Formats:</label>
                                        <select class="form-select @error('format') is-invalid @enderror" wire:model="format">
                                            <option value="csv">Excel</option>
                                            <option value="spss16">SPSS</option>
                                            <option value="fwu">Triple-S</option>
                                        </select>
                                        @error('format') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Layout:</label>
                                        <select class="form-select @error('layout') is-invalid @enderror" wire:model="layout" @if(empty($layouts)) disabled @endif>

                                            <option value="standard">Standard</option>
                                            @foreach($layouts as $layout)
                                                <option value="{{ $layout['id'] }}">(#{{ $layout['id'] }}) {{ $layout['description'] }}</option>
                                            @endforeach

                                        </select>
                                        @error('layout') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                <input class="form-check-input" type="radio" wire:model="condition" value="qualified" checked />
                                                <span class="form-check-label">Qualified respondents</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" wire:model="condition" value="disqualified" />
                                                <span class="form-check-label">Disqualified respondents</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" wire:model="condition" value="all" />
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
                                        <textarea class="form-control @error('emails') is-invalid @enderror" wire:model="emails" placeholder="JonJones@gmail.com, AlexPereira@kantar.com, ..." required></textarea>
                                        @error('emails') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="repeat">Repeat:</label>
                                        <select class="form-select @error('repeat') is-invalid @enderror" id="repeat" wire:model="repeat">
                                            <option value="* * * * *">Every minute</option>
                                            <option value="0 9,21 * * *">Every day at 9am and 9pm (GMT)</option>
                                            <option value="0 9 * * *">Every day at 9am (GMT)</option>
                                            <option value="0 9 */3 * *">Every 3rd day at 9am (GMT)</option>
                                            <option value="0 9 * * 1">Every Monday at 9am (GMT)</option>                                                        
                                        </select>
                                        @error('repeat') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            <input type="datetime-local" id="start_date" wire:model="startDate" class="form-control @error('startDate') is-invalid @enderror" required />
                                        </div>
                                        @error('startDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        
                                        <label for="end_date" class="form-label">End time:</label>
                                        <div class="input-group">
                                            <input type="datetime-local" id="end_date" wire:model="endDate" class="form-control @error('endDate') is-invalid @enderror" required />
                                        </div>
                                        @error('endDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- End row -->
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary" @if(!$isValidated) disabled @endif>
                Save the Decipher Auto Export
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    const apiKeyModal = new bootstrap.Modal(document.getElementById('apiKeyModal'));

    Livewire.on('showApiKeyModal', () => {
        apiKeyModal.show();
    });

    Livewire.on('hideApiKeyModal', () => {
        apiKeyModal.hide();
    });
});
</script>
@endpush
