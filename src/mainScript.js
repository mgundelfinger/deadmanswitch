// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'

function main() {
	// we get the data injected via the Initial State mechanism
	const state = loadState('deadmanswitch', 'tutorial_initial_state')

	document.getElementById('intervalSelector').value = state.check_in_interval

	modifySaveButton(state)
}

function modifySaveButton(state) {
	const saveButton = document.getElementById('saveInterval')
	saveButton.addEventListener('click', (e) => {
		const url = generateUrl('/apps/deadmanswitch/config')
		const params = {
			key: 'check_in_interval',
			value: document.getElementById('intervalSelector').value,
		}
		axios.put(url, params)
			.then((response) => {
				showSuccess('Settings saved: ' + response.data.message)
			})
			.catch((error) => {
				showError('Failed to save settings: ' + error.response.data.error_message)
				console.error(error)
			})
	})
}

// we wait for the page to be fully loaded
document.addEventListener('DOMContentLoaded', (event) => {
	main()
})
