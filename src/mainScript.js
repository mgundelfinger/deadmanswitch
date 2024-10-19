// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'
import DataTable from 'datatables.net-dt';

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

function updateForm(state) {
	document.getElementById('intervalSelector').value = state.check_in_interval
	document.getElementById('onOffSwitch').checked = !!+state.active
}

/**
 *
 * @param {any} state Initial State
 */
function setSaveAction(state) {
	const saveButton = document.getElementById('saveButton')
	const activeToggle = document.getElementById('onOffSwitch')
	const intervalSelector = document.getElementById('intervalSelector')
	saveButton.addEventListener('click', (e) => {
		e.preventDefault()
		const url = generateUrl('/apps/deadmanswitch/config/' + intervalSelector.value + '/' + (activeToggle.checked ? '1' : '0'))
		axios.put(url)
			.then((response) => {
				state.check_in_interval = intervalSelector.value
				state.active = (activeToggle.checked ? '1' : '0')
				updateForm(state)
				showSuccess(response.data.message)
			})
			.catch((error) => {
				showError('Failed to save settings: ' + error.response.data.error_message)
			})
	})
}

/**
 *
 * @param {any} state Initial State
 */
function setCancelAction(state) {
	const cancelButton = document.getElementById('cancelButton')
	cancelButton.addEventListener('click', (e) => {
		e.preventDefault()
		updateForm(state)
		toggleFormElements()
		toggleSave(state)
	})
}

/**
 *
 * @param {any} state Initial State
 */
function setActiveAction(state) {
	const activeToggle = document.getElementById('onOffSwitch')
	const config = document.getElementById('config')
	activeToggle.addEventListener('click', (e) => {
		for (const element of config.querySelectorAll('*[id]')) {
			if (activeToggle.checked) {
				element.disabled = false
			} else {
				element.disabled = true
			}
		}
		toggleFormElements()
		toggleSave(state)
	})
}

/**
 *
 * @param {any} state Initial State
 */
function addFormListener(state) {
	const config = document.getElementById('config')
	for (const element of config.querySelectorAll('*[id]')) {
		element.addEventListener('click', (e) => {
			toggleSave(state)
		})
	}
}

function toggleFormElements() {
	const isActive = document.getElementById('onOffSwitch').checked
	const config = document.getElementById('config')
	for (const element of config.querySelectorAll('*[id]')) {
		element.disabled = !isActive
	}
}

/**
 *
 * @param {any} state Initial State
 */
function toggleSave(state) {
	const activeToggle = document.getElementById('onOffSwitch')
	const intervalSelector = document.getElementById('intervalSelector')
	const saveButton = document.getElementById('saveButton')
	const cancelButton = document.getElementById('cancelButton')
	if (!activeToggle.checked && !!!+state.active && intervalSelector.value !== state.check_in_interval) {
		saveButton.disabled = true
		cancelButton.disabled = false
	} else if (activeToggle.checked === !!+state.active && intervalSelector.value === state.check_in_interval) {
		saveButton.disabled = true
		cancelButton.disbled = true
	} else {
		saveButton.disabled = false
		cancelButton.disabled = false
	}
}

// we wait for the page to be fully loaded
document.addEventListener('DOMContentLoaded', (event) => {
	main()

	if(jQuery('#jobs-table').length) {
		let table = new DataTable('#jobs-table', {
			'ajax': 'get-jobs',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "emailSubject" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#contacts-table').length) {
		let table = new DataTable('#contacts-table', {
			'ajax': 'get-contacts',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "firstName" },
				{ "data": "lastName" },
				{ "data": "email" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#contact-groups-table').length) {
		let table = new DataTable('#contact-groups-table', {
			'ajax': 'get-contact-groups',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#triggers-table').length) {
		let table = new DataTable('#triggers-table', {
			'ajax': 'get-triggers',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "delay" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#alive-statuses-table').length) {
		let table = new DataTable('#alive-statuses-table', {
			'ajax': 'get-alive-statuses',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#checkup-intervals-table').length) {
		let table = new DataTable('#checkup-intervals-table', {
			'ajax': 'get-checkup-intervals',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "interval" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#job-groups-table').length) {
		let table = new DataTable('#job-groups-table', {
			'ajax': 'get-job-groups',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#confirmators-table').length) {
		let table = new DataTable('#confirmators-table', {
			'ajax': 'get-confirmators',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "contact" },
				{ "data": "interval" },
				{ "data": "actions" },
			]
		});
	}

	if(jQuery('#confirmator-groups-table').length) {
		let table = new DataTable('#confirmator-groups-table', {
			'ajax': 'get-confirmator-groups',
			'processing': true,
			'serverSide': true,
			"columns": [
				{ "data": "name" },
				{ "data": "actions" },
			]
		});
	}

})

jQuery(document).on('click', '.confirm-action', function (e) {
	if(confirm("Are you sure ?") === true) {
		return true;
	}
	return false;
})
