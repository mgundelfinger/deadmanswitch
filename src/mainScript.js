// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import DataTable from 'datatables.net-dt';
import jQuery from 'jquery';

import 'datatables.net';
import 'datatables.net-bs';
import 'bootstrap-css';


// we wait for the page to be fully loaded
document.addEventListener('DOMContentLoaded', (event) => {

	if (jQuery('#jobs-table').length) {
		new DataTable('#jobs-table', {
			ajax: 'get-jobs',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'emailSubject' },
				{ data: 'actions' },
			],
		});
	}

	if (jQuery('#contacts-table').length) {
		 new DataTable('#contacts-table', {
			ajax: 'get-contacts',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'firstName' },
				{ data: 'lastName' },
				{ data: 'email' },
				{ data: 'actions' },
			],
		});
	}

	if (jQuery('#contact-groups-table').length) {
		new DataTable('#contact-groups-table', {
			ajax: 'get-contact-groups',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'actions' },
			],
		});
	}

	if (jQuery('#job-groups-table').length) {
		new DataTable('#job-groups-table', {
			ajax: 'get-job-groups',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'actions' },
			],
		});
	}

	if (jQuery('#tasks-table').length) {
		new DataTable('#tasks-table', {
			ajax: 'get-tasks',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'active' },
				{ data: 'contactGroup' },
				{ data: 'jobGroup' },
				{ data: 'deathDays' },
				{ data: 'actions' },
			],
		});
	}

});

jQuery(document).on('click', '.confirm-action', function(e) {
	if (confirm('Are you sure ?') === true) {
		return true;
	}
	return false;
});
