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
		<div class="data-settings-heading">
			<h2>
				{{ t('data_service', 'Data Service') }}
			</h2>
		</div>
		<br>
		<div class="data-settings-content">
			<div class="data-settings-hint">
				{{ t('data_service', 'List of provisioned services') }}
			</div>
			<br>
			<div v-if="configuredServices.length > 0" class="data-settings-content-list">
				<table>
					<tbody>
						<tr v-for="(item, index) in configuredServices" :key="index">
							<td>{{ item.service_name }}</td>
							<td>{{ item.service_id }}</td>
							<td>{{ item.service_token }}</td>
							<td>{{ item.collection_type }}</td>
							<td>{{ item.collection_id }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-else class="data-settings-content-empty">
				<h3>{{ t('data_service', 'No data services have been created for this account') }}</h3>
			</div>
			<br>
			<div class="data-settings-content-actions">
				<NcButton class="app-settings-button" @click="onAddClick()">
					<template #icon>
						<IconAdd :size="24" />
					</template>
					Add
				</NcButton>
			</div>
		</div>
		<template>
			<div>
				<NcModal v-if="dialogServiceSettings" name="Data Service Settings" @close="onCancelClick()">
					<div class="data-settings-modal-content">
						<h2>Data Service Settings</h2>
						<div class="data-settings-modal-group">
							<label for="data-service-id">
								{{ t('data_service', 'Service Id') }}
							</label>
							<input id="data-service-id"
								v-model="selectedServiceId"
								type="text"
								:placeholder="t('data_service', 'Service Id')"
								autocomplete="off"
								autocorrect="off"
								autocapitalize="none">
						</div>
						<div class="data-settings-modal-group">
							<label for="data-service-token">
								{{ t('data_service', 'Service Token') }}
							</label>
							<input id="data-service-token"
								v-model="selectedServiceToken"
								type="text"
								:placeholder="t('data_service', 'Service Token')"
								autocomplete="off"
								autocorrect="off"
								autocapitalize="none">
						</div>
						<div class="data-settings-modal-group">
							<label for="data-service-name">
								{{ t('data_service', 'Service Name') }}
							</label>
							<input id="data-service-name"
								v-model="selectedServiceId"
								type="text"
								:placeholder="t('data_service', 'Service Name')"
								autocomplete="off"
								autocorrect="off"
								autocapitalize="none">
						</div>
						<div>
							<label for="data-service-collection-type">
								{{ t('integration_ews', 'Source') }}
							</label>
							<NcSelect id="data-service-collection-type"
								v-model="selectedCollectionType"
								:reduce="item => item.id"
								:options="[{label: 'Contacts', id: 'CC'}, {label: 'Calendar', id: 'EC'}, {label: 'Tasks', id: 'TC'}]"
								@option:selected="onCollectionTypeChanged()" />
						</div>
						<div>
							<label for="data-service-collection-id">
								{{ t('integration_ews', 'Collection') }}
							</label>
							<NcSelect id="data-service-collection-id"
								v-model="selectedCollectionId"
								:options="availableCollections" />
						</div>
						<div>
							<label for="data-service-format">
								{{ t('integration_ews', 'Format') }}
							</label>
							<NcSelect id="data-service-format"
								v-model="selectedFormat"
								:reduce="item => item.id"
								:options="availableFormats" />
						</div>
						<div class="data-settings-modal-group">
							<label for="data-service-restriction-ip">
								{{ t('data_service', 'Restrict By IP') }}
							</label>
							<input id="data-service-restriction-ip"
								v-model="selectedServiceRestrictionIP"
								type="text"
								:placeholder="t('data_service', 'e.g. 172.0.0.1 10.0.0.0/24')"
								autocomplete="off"
								autocorrect="off"
								autocapitalize="none">
						</div>
						<div class="data-settings-modal-group">
							<label for="data-service-restriction-mac">
								{{ t('data_service', 'Restrict By MAC') }}
							</label>
							<input id="data-service-restriction-mac"
								v-model="selectedServiceRestrictionMAC"
								type="text"
								:placeholder="t('data_service', 'e.g. cd:39:53:de:a9:82')"
								autocomplete="off"
								autocorrect="off"
								autocapitalize="none">
						</div>
						<div>
							<NcButton @click="onSaveClick()">
								<template #icon>
									<IconSave />
								</template>
								{{ t('data_service', 'Save') }}
							</NcButton>
							<NcButton @click="onCancelClick()">
								<template #icon>
									<IconCancel />
								</template>
								{{ t('data_service', 'Cancel') }}
							</NcButton>
						</div>
					</div>
				</NcModal>
			</div>
		</template>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
// import { loadState } from '@nextcloud/initial-state'
import { showSuccess, showError } from '@nextcloud/dialogs'

import NcModal from '@nextcloud/vue/dist/Components/NcModal.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js'
import IconAdd from 'vue-material-design-icons/DatabaseExportOutline.vue'
import IconSave from 'vue-material-design-icons/Check.vue'
import IconCancel from 'vue-material-design-icons/Close.vue'

export default {
	name: 'UserSettings',

	components: {
		NcModal,
		NcButton,
		NcSelect,
		IconAdd,
		IconSave,
		IconCancel,
	},

	props: [],

	data() {
		return {
			// state: loadState('data_service', 'user-configuration'),
			dialogServiceSettings: false,
			dialogAction: 1,
			availableFormats: [],
			availableCollections: [],
			configuredServices: [],
			selectedServiceId: '',
			selectedServiceToken: '',
			selectedServiceName: '',
			selectedCollectionType: '',
			selectedCollectionId: '',
			selectedFormat: '',
			selectedServiceRestrictionIP: '',
			selectedServiceRestrictionMAC: '',
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
			this.listServices()
		},
		onAddClick() {
			this.clearSelected()
			this.dialogServiceSettings = true
		},
		onEditClick(id) {
			this.clearSelected()
			this.dialogServiceSettings = true
		},
		onSaveClick() {
			this.clearSelected()
			this.dialogServiceSettings = false
			showSuccess('Saved')
		},
		onCancelClick() {
			this.clearSelected()
			this.dialogServiceSettings = false
		},
		onCollectionTypeChanged() {
			this.listCollections()
			this.listFormats()
			this.selectedCollectionId = ''
			this.selectedFormat = ''
		},
		clearSelected() {
			this.selectedServiceId = ''
			this.selectedServiceToken = ''
			this.selectedServiceName = ''
			this.selectedCollectionType = ''
			this.selectedCollectionId = ''
			this.selectedFormat = ''
			this.selectedServiceRestrictionIP = ''
			this.selectedServiceRestrictionMAC = ''
		},
		listCollections() {
			const uri = generateUrl('/apps/data/list-collections')
			const data = {
				params: {
					type: this.selectedCollectionType,
				},
			}
			axios.get(uri, data)
				.then((response) => {
					if (response.data.Collections) {
						this.availableCollections = response.data.Collections
					}
				})
				.catch((error) => {
					showError(
						t('integration_ews', 'Failed to retrieve collections list')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		listFormats() {
			const uri = generateUrl('/apps/data/list-formats')
			const data = {
				params: {
					type: this.selectedCollectionType,
				},
			}
			axios.get(uri, data)
				.then((response) => {
					if (response.data.Formats) {
						this.availableFormats = response.data.Formats
					}
				})
				.catch((error) => {
					showError(
						t('integration_ews', 'Failed to retrieve formats list')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		listServices() {
			const uri = generateUrl('/apps/data/list-services')
			axios.get(uri)
				.then((response) => {
					if (response.data) {
						this.configuredServices = response.data
					}
				})
				.catch((error) => {
					showError(
						t('integration_ews', 'Failed to retrieve formats list')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
	},
}
</script>

<style scoped>
#ds_settings {
	.data-settings-heading {
		display:inline;
		vertical-align:middle;
	}
	.data-settings-content-empty {
		text-align: center;
		vertical-align:middle;
	}
	.data-settings-content-empty h3 {
		text-align: center;
		vertical-align:middle;
	}
	.data-settings-content-list table{
		width: auto;
	}
	.data-settings-content-list td{
		width: auto;
		padding: 10px;
	}
	.data-settings-button {
		display:inline-flex;
		vertical-align:middle;
	}
	.data-settings-hint {
		margin-top: -12px;
		margin-bottom: 6px;
		color: var(--color-text-maxcontrast);
	}
	.data-settings-modal-content {
		margin: 50px;
		padding: 50px;
	}
	.data-settings-modal-content h2 {
		text-align: center;
	}
	.data-settings-modal-group {
		margin: calc(var(--default-grid-baseline) * 4) 0;
		display: flex;
		flex-direction: column;
		align-items: flex-start;
	}
	.data-settings-modal-actions {
		display: flex;
		align-items: center;
	}
}
</style>
