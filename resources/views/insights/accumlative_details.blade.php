@extends('layout.main')

@section('content')
<div class="page-content1">

  <h2><i class="glyphicon glyphicon-user"></i> {{ $userData[0]->name }}</h2>
    <div class="row">

    @if ($isRep == "t")
      <div class="col-md-2 col-xs-6">
          <button class="buttonallsite3" ng-class="{'active': activeTab=='rep_areas'}" ng-click="showRepareas()">Private Market Visits [By Area]</button>
      </div>
    @elseif ($w == "general")
      <div class="col-md-2 col-xs-6">
          <button class="buttonallsite3" ng-class="{'active': activeTab=='general_samples'}" ng-click="showSamples()">Samples Insights</button>
      </div>
    @else
      <div class="col-md-2 col-xs-6">
          <button class="buttonallsite3" ng-class="{'active': activeTab=='doctors'}" ng-click="showDoctors()">Visits</button>
      </div>

      <div class="col-md-2 col-xs-6">
          <button class="buttonallsite3" ng-class="{'active': activeTab=='types'}" ng-click="showTypes()">Visits Types Count</button>
      </div>

      <div class="col-md-2 col-xs-6">
          <button class="buttonallsite3" ng-class="{'active': activeTab=='evals'}" ng-click="showEvals()">Reps Evaluations</button>
      </div>

    <div class="col-md-2 col-xs-6">
        <button class="buttonallsite3" ng-class="{'active': activeTab=='repoveralls'}" ng-click="showRepoveralls()">Reps Overall Report</button>
    </div>

    <div class="col-md-2 col-xs-6">
        <button class="buttonallsite3" ng-class="{'active': activeTab=='teams'}" ng-click="showTeams()">Team's Weekly Report</button>
    </div>
  @endif

    </div>

      <div class="col-sm-12">
          <table st-safe-src="doctorsCollection" st-table="displayDoctorsCollection" class="table table-striped table-bordered" id="doctorsTable">
              <thead>
              <tr>
                  <th st-sort="name">name</th>
                  <th st-sort="speciality">speciality</th>
                  <th st-sort="grade">Class</th>
                  @foreach($months as $month)
                      <th colspan="5">{{$month}}</th>
                  @endforeach
              </tr>
              <tr>
                  <th><input st-search="name" placeholder="Search for Name" class="input-sm form-control" type="search"/></th>
                  <th>
                      <select st-search="speciality" class="form-control">
                          <option value="">- All -</option>
                          <option ng-repeat="row in doctorsCollection | unique:'speciality'" value="[[row.speciality]]">[[row.speciality]]</option>
                      </select>
                  </th>
                  <th>
                      <select st-search="grade" class="form-control">
                          <option value="">- All -</option>
                          <option ng-repeat="row in doctorsCollection | unique:'grade'" value="[[row.grade]]">[[row.grade]]</option>
                      </select>
                  </th>

                  @foreach($months as $month)
                      <th>W1</th>
                      <th>W2</th>
                      <th>W3</th>
                      <th>W4</th>
                      <th>W5</th>
                  @endforeach
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displayDoctorsCollection">
                  <td>[[$index+1]]. [[row.name]]</td>
                  <td>[[row.speciality]]</td>
                  <td>[[row.grade]]</td>
                  @foreach($months as $monthNum=>$month)
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==1">[[visit.text_before]][[visit.day]][[visit.text_after]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==2">[[visit.text_before]][[visit.day]][[visit.text_after]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==3">[[visit.text_before]][[visit.day]][[visit.text_after]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==4">[[visit.text_before]][[visit.day]][[visit.text_after]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==5">[[visit.text_before]][[visit.day]][[visit.text_after]]</span>
                          </div>
                      </td>
                  @endforeach
              </tr>
              </tbody>
          </table>
          <table class="table table-striped table-bordered" id="typesTable">
            <thead>
              <tr>
                <th>Signle Visits Count</th>
                <th>Double Visits Count</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
            <tr ng-repeat="row in displayTypesCollection">
              <td>[[row.v_single]]</td>
              <td>[[row.v_double]]</td>
              <td>[[row.total]]</td>
            </tr>
            </tbody>
          </table>
          <table st-safe-src="evalsCollection" st-table="displayEvalsCollection" class="table table-striped table-bordered" id="evalsTable">
              <thead>
              <tr>
                  <th st-sort="Rep Name">name</th>
                  @foreach($months as $month)
                      <th colspan="5">{{$month}}</th>
                  @endforeach
              </tr>
              <tr>
                  <th><input st-search="name" placeholder="Search for Name" class="input-sm form-control" type="search"/></th>

                  @foreach($months as $month)
                      <th>W1</th>
                      <th>W2</th>
                      <th>W3</th>
                      <th>W4</th>
                      <th>W5</th>
                  @endforeach
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displayEvalsCollection">
                  <td><a href="../../../evals/{{$company}}/[[row.rep_id]]"> [[$index+1]]. [[row.name]] </a></td>
                  @foreach($months as $monthNum=>$month)
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==1">[[visit.day]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==2">[[visit.day]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==3">[[visit.day]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==4">[[visit.day]]</span>
                          </div>
                      </td>
                      <td>
                          <div ng-repeat="visit in row.visits">
                              <span class="[[visit.time]]" ng-show="visit.month=={{$monthNum}} && visit.week==5">[[visit.day]]</span>
                          </div>
                      </td>
                  @endforeach
              </tr>
              </tbody>
          </table>
          <table st-safe-src="repoverallsCollection" st-table="displayRepoverallsCollection" class="table table-striped table-bordered" id="repoverallsTable">
              <thead>
              <tr>
                  <th st-sort="Rep Name">Rep. Name</th>
                  <th st-sort="test">Area / City</th>
                  <th st-sort="test">Total visits</th>
                  <th colspan=3 st-sort="test">By Visit Type</th>
                  <th colspan=16 st-sort="test">By Doctor Speciality</th>
              </tr>
              <tr>
                  <th st-sort="test"></th>
                  <th st-sort="test"></th>
                  <th st-sort="test"></th>
                  <th st-sort="test">Private Market</th>
                  <th st-sort="test">Pharmacies</th>
                  <th st-sort="test">Hospitals</th>
                  <th st-sort="test">#ORS</th>
                  <th st-sort="test">#GP</th>
                  <th st-sort="test">#IM</th>
                  <th st-sort="test">#S</th>
                  <th st-sort="test">#U</th>
                  <th st-sort="test">#N</th>
                  <th st-sort="test">#ON</th>
                  <th st-sort="test">#ENT</th>
                  <th st-sort="test">#DEN</th>
                  <th st-sort="test">#GE</th>
                  <th st-sort="test">#PUD</th>
                  <th st-sort="test">#GYN</th>
                  <th st-sort="test">#RHU</th>
                  <th st-sort="test">#ID</th>
                  <th st-sort="test">#Others</th>
                  <th st-sort="test">Total</th>
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displayRepoverallsCollection">
                  <td>[[$index+1]]. [[row.name]]</td>
                  <td>[[row.governorate]]</td>
                  <td>[[row.t_total]]</td>
                  <td>[[row.t1]]</td>
                  <td>[[row.t2]]</td>
                  <td>[[row.t3]]</td>
                  <td>[[row.s_ors]]</td>
                  <td>[[row.s_gp]]</td>
                  <td>[[row.s_im]]</td>
                  <td>[[row.s_s]]</td>
                  <td>[[row.s_u]]</td>
                  <td>[[row.s_n]]</td>
                  <td>[[row.s_on]]</td>
                  <td>[[row.s_ent]]</td>
                  <td>[[row.s_den]]</td>
                  <td>[[row.s_ge]]</td>
                  <td>[[row.s_pud]]</td>
                  <td>[[row.s_gyn]]</td>
                  <td>[[row.s_rhu]]</td>
                  <td>[[row.s_id]]</td>
                  <td>[[row.s_others]]</td>
                  <td>[[row.s_total]]</td>
              </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th st-sort="test">Total</th>
                  <th st-sort="test"></th>
                  <th st-sort="test">[[getRepoverallsTotal("t_total")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("t1")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("t2")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("t3")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_ors")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_gp")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_im")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_s")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_u")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_n")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_on")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_ent")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_den")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_ge")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_pud")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_gyn")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_rhu")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_id")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_others")]]</th>
                  <th st-sort="test">[[getRepoverallsTotal("s_total")]]</th>
                </tr>
              </tfoot>
          </table>
          <table st-safe-src="repareasCollection" st-table="displayRepareasCollection" class="table table-striped table-bordered" id="repareasTable">
              <thead>
              <tr>
                  <th st-sort="Rep Name">Area</th>
                  <th colspan=3 st-sort="test">#ORS</th>
                  <th colspan=3 st-sort="test">#GP</th>
                  <th colspan=3 st-sort="test">#IM</th>
                  <th colspan=3 st-sort="test">#S</th>
                  <th colspan=3 st-sort="test">#U</th>
                  <th colspan=3 st-sort="test">#N</th>
                  <th colspan=3 st-sort="test">#ON</th>
                  <th colspan=3 st-sort="test">#ENT</th>
                  <th colspan=3 st-sort="test">#DEN</th>
                  <th colspan=3 st-sort="test">#GE</th>
                  <th colspan=3 st-sort="test">#PUD</th>
                  <th colspan=3 st-sort="test">#GYN</th>
                  <th colspan=3 st-sort="test">#RHU</th>
                  <th colspan=3 st-sort="test">#ID</th>
                  <th st-sort="test">#Others</th>
                  <th st-sort="test">Total</th>
              </tr>
              <tr>
                  <th st-sort="Rep Name">Area</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">A</th>
                  <th st-sort="test">B</th>
                  <th st-sort="test">C</th>
                  <th st-sort="test">#Others</th>
                  <th st-sort="test">Total</th>
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displayRepareasCollection">
                <td> [[$index+1]]. [[row.area]] </td>

                <td> [[row.s_ors_a]] </td>
                <td> [[row.s_ors_b]] </td>
                <td> [[row.s_ors_c]] </td>

                <td> [[row.s_gp_a]] </td>
                <td> [[row.s_gp_b]] </td>
                <td> [[row.s_gp_c]] </td>

                <td> [[row.s_im_a]] </td>
                <td> [[row.s_im_b]] </td>
                <td> [[row.s_im_c]] </td>

                <td> [[row.s_s_a]] </td>
                <td> [[row.s_s_b]] </td>
                <td> [[row.s_s_c]] </td>

                <td> [[row.s_u_a]] </td>
                <td> [[row.s_u_b]] </td>
                <td> [[row.s_u_c]] </td>

                <td> [[row.s_n_a]] </td>
                <td> [[row.s_n_b]] </td>
                <td> [[row.s_n_c]] </td>

                <td> [[row.s_on_a]] </td>
                <td> [[row.s_on_b]] </td>
                <td> [[row.s_on_c]] </td>

                <td> [[row.s_ent_a]] </td>
                <td> [[row.s_ent_b]] </td>
                <td> [[row.s_ent_c]] </td>

                <td> [[row.s_den_a]] </td>
                <td> [[row.s_den_b]] </td>
                <td> [[row.s_den_c]] </td>

                <td> [[row.s_ge_a]] </td>
                <td> [[row.s_ge_b]] </td>
                <td> [[row.s_ge_c]] </td>

                <td> [[row.s_pud_a]] </td>
                <td> [[row.s_pud_b]] </td>
                <td> [[row.s_pud_c]] </td>

                <td> [[row.s_gyn_a]] </td>
                <td> [[row.s_gyn_b]] </td>
                <td> [[row.s_gyn_c]] </td>

                <td> [[row.s_rhu_a]] </td>
                <td> [[row.s_rhu_b]] </td>
                <td> [[row.s_rhu_c]] </td>

                <td> [[row.s_id_a]] </td>
                <td> [[row.s_id_b]] </td>
                <td> [[row.s_id_c]] </td>

                <td> [[row.s_others]] </td>
                <td> [[row.s_total]] </td>
              </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th st-sort="test">Total</th>

                  <th st-sort="test">[[getRepareasTotal("s_ors_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_ors_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_ors_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_gp_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_gp_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_gp_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_im_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_im_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_im_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_s_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_s_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_s_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_u_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_u_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_u_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_n_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_n_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_n_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_on_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_on_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_on_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_ent_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_ent_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_ent_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_den_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_den_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_den_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_ge_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_ge_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_ge_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_pud_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_pud_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_pud_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_gyn_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_gyn_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_gyn_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_rhu_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_rhu_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_rhu_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_id_a")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_id_b")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_id_c")]]</th>

                  <th st-sort="test">[[getRepareasTotal("s_others")]]</th>
                  <th st-sort="test">[[getRepareasTotal("s_total")]]</th>
                </tr>
              </tfoot>
          </table>

          <table st-safe-src="samplesCollection" st-table="displaySamplesCollection" class="table table-striped table-bordered" id="samplesTable">
              <thead>
              <tr>
                  <th st-sort="test">Product Name</th>
                  <th st-sort="test">Total Number of Samples</th>
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displaySamplesCollection">
                  <td>[[$index+1]]. [[row.pname]]</td>
                  <td>[[row.samples]]</td>
              </tr>
              </tbody>
          </table>
          <table st-safe-src="samples_customersCollection" st-table="displaySamples_customersCollection" class="table table-striped table-bordered" id="samples_customersTable">
              <thead>
              <tr>
                  <th st-sort="test">Product Name</th>
                  <th st-sort="pname">Visit Type</th>
                  <th st-sort="type">Total Number of Samples</th>
              </tr>
              <tr>
                <th>
                    <select st-search="pname" class="form-control">
                        <option value="">- All -</option>
                        <option ng-repeat="row in samples_customersCollection | unique:'pname'" value="[[row.pname]]">[[row.pname]]</option>
                    </select>
                </th>
                <th>
                    <select st-search="type" class="form-control">
                        <option value="">- All -</option>
                        <option ng-repeat="row in samples_customersCollection | unique:'type'" value="[[row.type]]">[[row.type]]</option>
                    </select>
                </th>
                <th>

                </th>
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displaySamples_customersCollection">
                  <td>[[$index+1]]. [[row.pname]]</td>
                  <td>[[row.type]]</td>
                  <td>[[row.samples]]</td>
              </tr>
              </tbody>
          </table>
          <table st-safe-src="samples_spCollection" st-table="displaySamples_spCollection" class="table table-striped table-bordered" id="samples_spTable">
              <thead>
              <tr>
                  <th st-sort="pname">Product Name</th>
                  <th st-sort="speciality">Speciality</th>
                  <th st-sort="test">Total Number of Samples</th>
              </tr>
              <tr>
                <th>
                    <select st-search="pname" class="form-control">
                        <option value="">- All -</option>
                        <option ng-repeat="row in samples_spCollection | unique:'pname'" value="[[row.pname]]">[[row.pname]]</option>
                    </select>
                </th>
                <th>
                    <select st-search="speciality" class="form-control">
                        <option value="">- All -</option>
                        <option ng-repeat="row in samples_spCollection | unique:'speciality'" value="[[row.speciality]]">[[row.speciality]]</option>
                    </select>
                </th>
                <th>

                </th>
              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displaySamples_spCollection">
                  <td>[[$index+1]]. [[row.pname]]</td>
                  <td>[[row.speciality]]</td>
                  <td>[[row.samples]]</td>
              </tr>
              </tbody>
          </table>
          <table st-safe-src="teamsCollection" st-table="displayTeamsCollection" class="table table-striped table-bordered" id="teamsTable">
              <thead>
              <tr>
                  <th st-sort="fullname">Rep Name</th>
                  <th st-sort="type">Type of Visit</th>
                  <th st-sort="test">{{ $teams_days[6] }}</th>
                  <th st-sort="test">{{ $teams_days[5] }}</th>
                  <th st-sort="test">{{ $teams_days[4] }}</th>
                  <th st-sort="test">{{ $teams_days[3] }}</th>
                  <th st-sort="test">{{ $teams_days[2] }}</th>
                  <th st-sort="test">{{ $teams_days[1] }}</th>
                  <th st-sort="test">{{ $teams_days[0] }}</th>
                  <th st-sort="test">Total</th>
              </tr>
              <tr>
                <th>
                    <select st-search="fullname" class="form-control">
                        <option value="">- All -</option>
                        <option ng-repeat="row in teamsCollection | unique:'fullname'" value="[[row.fullname]]">[[row.fullname]]</option>
                    </select>
                </th>
                <th>
                    <select st-search="type" class="form-control">
                        <option value="">- All -</option>
                        <option ng-repeat="row in teamsCollection | unique:'type'" value="[[row.type]]">[[row.type]]</option>
                    </select>
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>

              </tr>
              </thead>
              <tbody>
              <tr ng-repeat="row in displayTeamsCollection">
                  <td>[[$index+1]]. [[row.fullname]]</td>
                  <td>[[row.type]]</td>
                  <td>[[row.day1]]</td>
                  <td>[[row.day2]]</td>
                  <td>[[row.day3]]</td>
                  <td>[[row.day4]]</td>
                  <td>[[row.day5]]</td>
                  <td>[[row.day6]]</td>
                  <td>[[row.day7]]</td>
                  <td>[[row.total]]</td>
              </tr>
              </tbody>
          </table>
        </div>
      </div>
@endsection

@section('head_inc')
  <script src="{{asset('resources/assets/js/angular.min.js')}}"></script>
  <script src="{{asset('resources/assets/js/ui-bootstrap.min.js')}}"></script>
  <script src="{{asset('resources/assets/js/ui-bootstrap-tpls.min.js')}}"></script>
  <script src="{{asset('resources/assets/js/smart-table.js')}}"></script>

  <style>
      .table {
          display: none;
      }

      .AM {
          color: blue;
      }

      .PM {
          color: red;
      }
</style>
  </style>

  <script>
      var app = angular.module('myApp', ['ui.bootstrap', 'smart-table']);
      app.config(function ($interpolateProvider) {
          $interpolateProvider.startSymbol('[[');
          $interpolateProvider.endSymbol(']]');
      });
      function toArray(inputObj) {
          var output = [];
          for (var key in inputObj) {
              // must create a temp object to set the key using a variable
              var tempObj = {};
              tempObj[key] = inputObj[key];
              output.push(tempObj);
          }
          return output;
      }

      app.controller('gridCtrl', ['$scope', '$filter', '$http', function (scope, filter, $http) {

          scope.activeTab = 'doctors';
          scope.CurrentFirstName = "";
          scope.showDoctors = function () {
              $('.table').hide();
              scope.activeTab = 'doctors';
              if (typeof (scope.doctorsCollection) === 'undefined') {
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/'.$userData[0]->native_id."?type=doctors")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)

                          .then(function (response) {
                              //alert(response.data.sql);
                              scope.doctorsCollection = response.data.doctors;
                              scope.displayDoctorsCollection = [].concat(scope.doctorsCollection);
                              $('#doctorsTable').fadeIn();
                          });
              } else {
                  $('#doctorsTable').fadeIn();
              }

//                console.log(scope.doctorsCollection);
          };
          scope.showEvals = function () {
              $('.table').hide();
              scope.activeTab = 'evals';
              if (typeof (scope.evalsCollection) === 'undefined') {
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/'.$userData[0]->native_id."?type=evals")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)

                          .then(function (response) {
                              scope.evalsCollection = response.data.evals;
                              scope.displayEvalsCollection = [].concat(scope.evalsCollection);
                              $('#evalsTable').fadeIn();
                          });
              } else {
                  $('#evalsTable').fadeIn();
              }

  //                console.log(scope.doctorsCollection);
          };

          scope.showRepoveralls = function () {
              $('.table').hide();
              scope.activeTab = 'repoveralls';
              if (typeof (scope.repoverallsCollection) === 'undefined') {
                  //alert('');
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/'.$userData[0]->native_id."?type=repoveralls")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)

                          .then(function (response) {
                              scope.repoverallsCollection = response.data.repoveralls;
                              scope.displayRepoverallsCollection = [].concat(scope.repoverallsCollection);
                              $('#repoverallsTable').fadeIn();
                          });
              } else {
                  $('#repoverallsTable').fadeIn();
              }

  //                console.log(scope.doctorsCollection);
          };

          scope.showRepareas = function () {
              $('.table').hide();
              scope.activeTab = 'rep_areas';
              if (typeof (scope.repareasCollection) === 'undefined') {
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/'.$userData[0]->native_id."?type=repareas")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)

                          .then(function (response) {
                              scope.repareasCollection = response.data.repareas;
                              scope.displayRepareasCollection = [].concat(scope.repareasCollection);
                              $('#repareasTable').fadeIn();
                          });
              } else {
                  $('#repareasTable').fadeIn();
              }

  //                console.log(scope.doctorsCollection);
          };

          scope.showSamples = function () {
              $('.table').hide();
              scope.activeTab = 'general_samples';
              if (typeof (scope.samplesCollection) === 'undefined') {
                  //alert('');
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/o'."?type=samples")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)

                          .then(function (response) {
                            scope.samplesCollection = response.data.samples;
                            scope.samples_spCollection = response.data.samples_sp;
                            scope.samples_customersCollection = response.data.samples_customers;
                            scope.displaySamplesCollection = [].concat(scope.samplesCollection);
                            scope.displaySamples_spCollection = [].concat(scope.samples_spCollection);
                            scope.displaySamples_customersCollection = [].concat(scope.samples_customersCollection);
                            $('#samplesTable').fadeIn();
                            $('#samples_spTable').fadeIn();
                            $('#samples_customersTable').fadeIn();
                          });
              } else {
                $('#samplesTable').fadeIn();
                $('#samples_spTable').fadeIn();
                $('#samples_customersTable').fadeIn();
              }

  //                console.log(scope.doctorsCollection);
          };

          scope.showTeams = function () {
              $('.table').hide();
              scope.activeTab = 'teams';
              if (typeof (scope.teamsCollection) === 'undefined') {
                  //alert('');
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/'.$userData[0]->native_id."?type=teams")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)
                          .then(function (response) {
                            scope.teamsCollection = response.data.teams;
                            scope.displayTeamsCollection = [].concat(scope.teamsCollection);
                            $('#teamsTable').fadeIn();
                          });
              } else {
                $('#teamsTable').fadeIn();
              }

  //                console.log(scope.doctorsCollection);
          };

          scope.showTypes = function () {
              $('.table').hide();
              scope.activeTab = 'types';
              if (typeof (scope.typesCollection) === 'undefined') {
                  var data_url = '{{url('insights/accumulative-details/'.$company.'/'.$userData[0]->native_id."?type=types")}}&datefrom={{$startDate}}&dateto={{$endDate}}';
                  $http.get(data_url)
                          .then(function (response) {
                              scope.typesCollection = response.data.types;
                              scope.displayTypesCollection = [].concat(scope.typesCollection);
                              $('#typesTable').fadeIn();
                          });
              } else {
                  $('#typesTable').fadeIn();
              }

  //                console.log(scope.doctorsCollection);
          };

          scope.getRepoverallsTotal = function(col){
            var total = 0;
            for(var i = 0; i < scope.displayRepoverallsCollection.length; i++){
              var repoverall = scope.displayRepoverallsCollection[i];
              total += Number(repoverall[col]);
            }
              return total;
          };

          scope.getRepareasTotal = function(col){
            var total = 0;
            for(var i = 0; i < scope.displayRepareasCollection.length; i++){
              var row = scope.displayRepareasCollection[i];
              total += Number(row[col]);
            }
              return total;
          };

          scope.OnlyFirst = function(str){
            if (str == scope.CurrentFirstName){
              return "E";
            }else{
              scope.CurrentFirstName = str;
              return str;
            }
          };


          @if ($isRep == "t")
          scope.showRepareas();
          @elseif ($w == "general")
          scope.showSamples();
          @else
          scope.showDoctors();
          @endif
        }]);

        app.filter('myStrictFilter', function ($filter) {
            return function (input, predicate) {
                return $filter('filter')(input, predicate, true);
            }
        });

        app.filter('unique', function () {
            return function (arr, field) {
                if (typeof (arr) === 'undefined') {
                    return '';
                }
                var o = {}, i, l = arr.length, r = [];
                for (i = 0; i < l; i += 1) {
                    o[arr[i][field]] = arr[i];
                }
                for (i in o) {
                    r.push(o[i]);
                }
                return r;
            };
        });


    </script>

@endsection
