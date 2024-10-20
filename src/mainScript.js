// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import DataTable from 'datatables.net-dt'
import jQuery from 'jquery'

import 'datatables.net'
import 'datatables.net-bs'
import 'bootstrap-css'

/**
 *
 */
function main() {
	// we get the data injected via the Initial State mechanism
	// const state = loadState('deadmanswitch', 'initial_state')
	//
	// updateForm(state)
	//
	// toggleSave(state)
	// toggleFormElements()
	// setSaveAction(state)
	// setCancelAction(state)
	// setActiveAction(state)
	// addFormListener(state)
}

// we wait for the page to be fully loaded
document.addEventListener('DOMContentLoaded', (event) => {
	main()

	if (jQuery('#jobs-table').length) {
		const table = new DataTable('#jobs-table', {
			ajax: 'get-jobs',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'emailSubject' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#contacts-table').length) {
		const table = new DataTable('#contacts-table', {
			ajax: 'get-contacts',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'firstName' },
				{ data: 'lastName' },
				{ data: 'email' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#contact-groups-table').length) {
		const table = new DataTable('#contact-groups-table', {
			ajax: 'get-contact-groups',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#triggers-table').length) {
		const table = new DataTable('#triggers-table', {
			ajax: 'get-triggers',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'delay' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#alive-statuses-table').length) {
		const table = new DataTable('#alive-statuses-table', {
			ajax: 'get-alive-statuses',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#checkup-intervals-table').length) {
		const table = new DataTable('#checkup-intervals-table', {
			ajax: 'get-checkup-intervals',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'interval' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#job-groups-table').length) {
		const table = new DataTable('#job-groups-table', {
			ajax: 'get-job-groups',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#confirmators-table').length) {
		const table = new DataTable('#confirmators-table', {
			ajax: 'get-confirmators',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'contact' },
				{ data: 'interval' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#confirmator-groups-table').length) {
		const table = new DataTable('#confirmator-groups-table', {
			ajax: 'get-confirmator-groups',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'actions' },
			],
		})
	}

	if (jQuery('#tasks-table').length) {
		const table = new DataTable('#tasks-table', {
			ajax: 'get-tasks',
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'name' },
				{ data: 'active' },
				{ data: 'contactGroup' },
				{ data: 'jobGroup' },
				{ data: 'confirmatorGroup' },
				{ data: 'trigger' },
				{ data: 'actions' },
			],
		})
	}

})

jQuery(document).on('click', '.confirm-action', function(e) {
	if (confirm('Are you sure ?') === true) {
		return true
	}
	return false
})
