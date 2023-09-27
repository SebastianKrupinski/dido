<!--
*
* @copyright Copyright (c) 2023 Sebastian Krupinski <krupinski01@gmail.com>
*
* @author Sebastian Krupinski <krupinski01@gmail.com>
*
* @license AGPL-3.0-or-later
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
-->

<template>
	<div id="ds_settings" class="section">
		<div class="ds-section-heading">
			<EwsIcon :size="32" /><h2> {{ t('data_service', 'Data Service') }}</h2>
		</div>
		<div class="ds-content">
			<div class="settings-hint">
				{{ t('data_service', 'List of provisioned services') }}
			</div>
			<div>

			</div>
			<div>
				<ul>
					<li v-for="ritem in availableRemoteContactCollections" :key="ritem.id" class="ds-collectionlist-item">
						<ContactIcon />
						<label>
							{{ ritem.name }} ({{ ritem.count }} Contacts)
						</label>
						<NcActions>
							<template #icon>
								<LinkIcon />
							</template>
							<NcActionButton @click="clearContactCorrelation(ritem.id)">
								<template #icon>
									<CloseIcon />
								</template>
								Clear
							</NcActionButton>
							<NcActionRadio v-for="litem in availableLocalContactCollections"
								:key="litem.id"
								:disabled="establishedContactCorrelationDisable(ritem.id, litem.id)"
								:checked="establishedContactCorrelationSelect(ritem.id, litem.id)"
								@change="changeContactCorrelation(ritem.id, litem.id)">
								{{ litem.name }}
							</NcActionRadio>
						</NcActions>
					</li>
				</ul>
			</div>
			<br>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import { showSuccess, showError } from '@nextcloud/dialogs'

import NcActions from '@nextcloud/vue/dist/Components/NcActions.js'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'
import NcActionRadio from '@nextcloud/vue/dist/Components/NcActionRadio.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js'

import EwsIcon from './icons/EwsIcon.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import CalendarIcon from 'vue-material-design-icons/Calendar.vue'
import ContactIcon from 'vue-material-design-icons/ContactsOutline.vue'
import LinkIcon from 'vue-material-design-icons/Link.vue'

export default {
	name: 'UserSettings',

	components: {
		NcActions,
		NcActionButton,
		NcActionRadio,
		NcButton,
		NcCheckboxRadioSwitch,
		NcSelect,
		EwsIcon,
		CheckIcon,
		CloseIcon,
		CalendarIcon,
		ContactIcon,
		LinkIcon,
	},

	props: [],

	data() {
		return {
			readonly: true,
			state: loadState('data_service', 'user-configuration'),
		}
	},

	computed: {
	},

	watch: {
	},

	mounted() {
		this.loadData()
	},

	methods: {
		loadData() {
		},
		onConnectAlternateClick() {
			const uri = generateUrl('/apps/data_service/connect-alternate')
			const data = {
				params: {
					account_id: this.state.account_id,
					account_secret: this.state.account_secret,
					account_server: this.state.account_server,
					flag: this.configureMail,
				},
			}
			axios.get(uri, data)
				.then((response) => {
					if (response.data === 'success') {
						showSuccess(('Successfully connected to EWS account'))
						this.state.account_connected = '1'
						this.fetchPreferences()
						this.loadData()
					}
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to authenticate with EWS server')
						+ ': ' + error.response?.request?.responseText
					)
				})
		},

		onSaveClick() {
		},
	},
}
</script>

<style scoped lang="scss">
#ds_settings {
	.ds-section-heading {
		display:inline-block;
		vertical-align:middle;
	}

	.ds-connected {
		display: flex;
		align-items: center;

		label {
			padding-left: 1em;
			padding-right: 1em;
		}
	}

	.ds-collectionlist-item {
		display: flex;
		align-items: center;

		label {
			padding-left: 1em;
			padding-right: 1em;
		}
	}

	.ds-actions {
		display: flex;
		align-items: center;
	}

	.external-label {
		display: flex;
		//width: 100%;
		margin-top: 1rem;
	}

	.external-label label {
		padding-top: 7px;
		padding-right: 14px;
		white-space: nowrap;
	}
}
</style>
