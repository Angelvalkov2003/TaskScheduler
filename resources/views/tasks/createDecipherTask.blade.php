<x-layout>
    
    <div class="container mt-5">
        <h2 class="text-center">Decipher Auto Export Create Page</h2>
  
        <div class="card">
          <div class="card-body">
            <div class="mb-3 w-50 mx-auto">
              <label class="form-label">Survey path:</label>
              <input class="form-control" placeholder="Please input the survey path..."/>
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
                                      <input class="form-control" placeholder="Type the name of your export..." />
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="card">
                                  <div class="card-body">
                                      <div class="mb-3">
                                          <label class="form-label">Formats:</label>
                                          <select class="form-select">
                                              <option>Excel</option>
                                              <option>SPSS</option>
                                              <option>Tripple-S</option>
                                              <option>JSON</option>
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
                                          <select class="form-select">
                                              <option>Standart</option>
                                              <option>OE data</option>
                                              <option>CE data</option>
                                              <option>Custom_format</option>
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
                                              <input class="form-check-input" type="radio" name="radios-inline" checked />
                                              <span class="form-check-label">Qualified respondents</span>
                                            </label>
                                            <!--<label class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="radios-inline" />
                                              <span class="form-check-label">Disqualified respondents</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="radios-inline" />
                                              <span class="form-check-label">All respondents</span>
                                            </label>-->
                                          </div>
                                        </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="card">
                                  <div class="card-body" style="height: 10rem">
                                      <div class="mb-3">
                                          <label class="form-label">Emails to receive:</label>
                                          <textarea
                                            class="form-control"
                                            name="example-textarea"
                                            placeholder="JohnJones@gmail.com, AlexPereira@kantar.com, ..."
                                          ></textarea>
                                        </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="card">
                                  <div class="card-body" style="height: 10rem">
                                    <div class="mb-3">
                                      <label class="form-label" for="repeat">Repeat:</label>
                                      <select class="form-select" id="repeat">
                                        <option>every day at 9am and 9pm (GMT)</option>
                                        <option>every day at 9am (GMT)</option>
                                        <option>every 3rd day at 9am (GMT)</option>
                                        <option>every Monday at 9am (GMT)</option>
                                        <option>open</option>
                                      </select>
                                    </div>
                                    
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="card">
                                  <div class="card-body" style="height: 10rem">
                                      <div class="mb-3">
                                          <label for="datetimepicker" class="form-label">Start time:</label>
                                          <div class="input-group">
                                              <input type="text" id="datetimepicker" class="form-control" />
                                              <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                          </div>
                                          <label for="datetimepicker" class="form-label">End time:</label>
                                          <div class="input-group">
                                              <input type="text" id="datetimepicker" class="form-control" />
                                              <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          
          <div class="text-end">
            <a href="#" class="btn btn-primary">Save the Decipher Auto Export</a>
        </div>
        
        
        
          
        </div> <!--card body-->
      </div><!--card-->
</x-layout>