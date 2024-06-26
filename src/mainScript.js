// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'

/**
 *
 */
function main() {
	// we get the data injected via the Initial State mechanism
	const state = loadState('deadmanswitch', 'initial_state')

	updateForm(state)

	toggleSave(state)
	toggleFormElements()
	setSaveAction(state)
	setCancelAction(state)
	setActiveAction(state)
	addFormListener(state)
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
})
