// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'
import DataTable from 'datatables.net-dt'

import 'datatables.net'
import 'datatables.net-bs'
// import 'datatables.net-bs/css/dataTables.bootstrap.css'
// import 'datatables.net-buttons'
// import 'datatables.net-buttons-bs'
// import 'datatables.net-buttons-bs/css/buttons.bootstrap.css'
// import 'datatables.net-buttons/js/buttons.colVis'
// import 'datatables.net-buttons/js/buttons.html5'
// // import 'datatables.net-buttons/js/buttons.flash'
// // import 'datatables.net-buttons/js/buttons.print'
// import 'datatables.net-colreorder'
// import 'datatables.net-colreorder-bs'
// import 'datatables.net-colreorder-bs/css/colReorder.bootstrap.css'
// import 'datatables.net-fixedcolumns'
// import 'datatables.net-fixedcolumns-bs'
// import 'datatables.net-fixedcolumns-bs/css/fixedColumns.bootstrap.css'
// import 'datatables.net-fixedheader'
// import 'datatables.net-fixedheader-bs'
// import 'datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.css'
// import 'datatables.net-rowgroup'
// import 'datatables.net-rowgroup-bs'
// import 'datatables.net-rowgroup-bs/css/rowGroup.bootstrap.css'
// import 'datatables.net-rowreorder'
// import 'datatables.net-rowreorder-bs'
// import 'datatables.net-rowreorder-bs/css/rowReorder.bootstrap.css'
// import 'datatables.net-responsive'
// import 'datatables.net-responsive-bs'
// import 'datatables.net-responsive-bs/css/responsive.bootstrap.css'
// import 'datatables.net-scroller'
// import 'datatables.net-scroller-bs'
// import 'datatables.net-scroller-bs/css/scroller.bootstrap.css'
// import 'datatables.net-select'
// import 'datatables.net-select-bs'
// import 'datatables.net-select-bs/css/select.bootstrap.css'
//
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

/**
 *
 * @param {any} state Initial State
 */
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

/**
 *
 */
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

	const table = new DataTable('#jobsTable', {
		ajax: 'get-jobs',
		processing: true,
		serverSide: true,
		columns: [
			{ data: 'name' },
			{ data: 'emailSubject' },
		],
	})

})
