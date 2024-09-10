<div class="tab-pane fade" id="team-report" role="tabpanel" aria-labelledby="team-report-tab">
    <div class="row">
        <!-- Project Selector -->
          <div class="col-md-12 mb-4">
                <div class="card">
                   <div class="card-header">Select Project</div>
                        <div class="card-body">
                            <form id="projectSelectorForm">
                                <div class="mb-3">
                                    <label for="projectSelect" class="form-label">Choose a Project</label>
                                    <select class="form-select" id="projectSelect" name="project_id">
                                     <!-- Populate with projects dynamically -->
                                                    @foreach($projects as $project)
                                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">View Report</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Performance Chart -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">Team Performance</div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="teamPerformanceChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>