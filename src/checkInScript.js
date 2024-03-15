// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
import { generateUrl } from '@nextcloud/router'

/**
 *
 */
function main() {
	setButtonAction()
}

/**
 *
 */
function setButtonAction() {
	const returnButton = document.getElementById('returnButton')
	returnButton.addEventListener('click', (e) => {
		window.location.href = generateUrl('/apps/deadmanswitch')
	})
}

// we wait for the page to be fully loaded
document.addEventListener('DOMContentLoaded', (event) => {
	main()
})
