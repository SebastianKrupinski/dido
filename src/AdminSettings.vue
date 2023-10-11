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
		<div class="data-settings-page-title">
			<IconApp :size="32" /><h2> {{ t('data_service', 'Data Service') }}</h2>
		</div>
		<br>
		<div class="data-settings-content">
			<div class="data-settings-section-system">
				<div class="data-settings-hint">
					{{ t('data_service', 'System Settings') }}
				</div>
				<div>
					<NcCheckboxRadioSwitch type="switch" :checked.sync="permissionsUserCreate" @update:checked="depositSettings">
						{{ t('data_service', 'Allow users to create services') }}
					</NcCheckboxRadioSwitch>
				</div>
				<div>
					<NcCheckboxRadioSwitch type="switch" :checked.sync="permissionsUserModify" @update:checked="depositSettings">
						{{ t('data_service', 'Allow users to modify services') }}
					</NcCheckboxRadioSwitch>
				</div>
				<div>
					<NcCheckboxRadioSwitch type="switch" :checked.sync="permissionsUserDelete" @update:checked="depositSettings">
						{{ t('data_service', 'Allow users to delete services') }}
					</NcCheckboxRadioSwitch>
				</div>
			</div>
			<div class="data-settings-section-services">
				<div class="data-settings-hint">
					{{ t('data_service', 'List of provisioned services') }}
				</div>
				<div v-if="configuredServices.length > 0" class="data-settings-content-list">
					<table>
						<thead>
							<tr>
								<th>User</th>
								<th>Id</th>
								<th>Token</th>
								<th>Name</th>
								<th>Type</th>
								<th>Collection</th>
								<th>Format</th>
								<th>Accessed On</th>
								<th>Accessed From</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in configuredServices" :key="index">
								<td>{{ item.uid }}</td>
								<td>{{ item.service_id }}</td>
								<td>{{ item.service_token }}</td>
								<td>{{ item.service_name }}</td>
								<td>{{ item.data_type }}</td>
								<td>{{ item.data_collection_name }}</td>
								<td>{{ item.format }}</td>
								<td>{{ formatDate(item.accessed_on) }}</td>
								<td>{{ item.accessed_from }}</td>
								<td>
									<NcActions>
										<template #icon>
											<IconActions />
										</template>
										<NcActionButton @click="onEditClick(item.id)">
											<template #icon>
												<IconEdit />
											</template>
											Edit
										</NcActionButton>
										<NcActionButton @click="onDeleteClick(item.id)">
											<template #icon>
												<IconDelete />
											</template>
											Delete
										</NcActionButton>
									</NcActions>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div v-else class="data-settings-content-empty">
					<h3>{{ t('data_service', 'No data services have been created for this account') }}</h3>
				</div>
			</div>
			<div class="data-settings-section-actions">
				<NcButton class="app-settings-button" @click="onAddClick()">
					<template #icon>
						<IconAdd :size="24" />
					</template>
					Add
				</NcButton>
			</div>
		</div>
		<div>
			<NcModal v-if="dialogServiceSettings" name="Data Service Settings" @close="onCancelClick()">
				<div class="data-settings-modal-content">
					<h2>Data Service Settings</h2>
					<div>
						<label for="data-service-user">
							{{ t('data_service', 'User') }}
						</label>
						<NcSelect id="data-service-user"
							v-model="selectedUser"
							:placeholder="t('data_service', 'User')"
							:reduce="item => item.id"
							:options="userList"
							@option:selected="onUserChanged()" />
					</div>
					<div class="data-settings-modal-group">
						<label for="data-service-id">
							{{ t('data_service', 'Id') }}
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
							{{ t('data_service', 'Token') }}
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
							{{ t('data_service', 'Name') }}
						</label>
						<input id="data-service-name"
							v-model="selectedServiceName"
							type="text"
							:placeholder="t('data_service', 'Service Name')"
							autocomplete="off"
							autocorrect="off"
							autocapitalize="none">
					</div>
					<div>
						<label for="data-service-data-type">
							{{ t('data_service', 'Type') }}
						</label>
						<NcSelect id="data-service-data-type"
							v-model="selectedDataType"
							:placeholder="t('data_service', 'Data Type')"
							:reduce="item => item.id"
							:options="availableTypes"
							@option:selected="onDataTypeChanged()" />
					</div>
					<div>
						<label for="data-service-data-collection">
							{{ t('data_service', 'Collection') }}
						</label>
						<NcSelect id="data-service-data-collection"
							v-model="selectedDataCollection"
							:placeholder="t('data_service', 'Data Collection')"
							:reduce="item => item.id"
							:options="availableCollections" />
					</div>
					<div>
						<label for="data-service-format">
							{{ t('data_service', 'Format') }}
						</label>
						<NcSelect id="data-service-format"
							v-model="selectedFormat"
							:placeholder="t('data_service', 'Output Format')"
							:reduce="item => item.id"
							:options="availableFormats" />
					</div>
					<div class="data-settings-modal-group">
						<label for="data-service-restrict-ip">
							{{ t('data_service', 'Restrict By IP') }}
						</label>
						<input id="data-service-restrict-ip"
							v-model="selectedServiceRestrictIP"
							type="text"
							:placeholder="t('data_service', 'e.g. 172.0.0.1 10.0.0.0/24')"
							autocomplete="off"
							autocorrect="off"
							autocapitalize="none">
					</div>
					<div class="data-settings-modal-group">
						<label for="data-service-restrict-mac">
							{{ t('data_service', 'Restrict By MAC') }}
						</label>
						<input id="data-service-restrict-mac"
							v-model="selectedServiceRestrictMAC"
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
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showSuccess, showError } from '@nextcloud/dialogs'

import NcActions from '@nextcloud/vue/dist/Components/NcActions.js'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'
import NcModal from '@nextcloud/vue/dist/Components/NcModal.js'
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js'
import IconApp from 'vue-material-design-icons/DatabaseOutline.vue'
import IconAdd from 'vue-material-design-icons/DatabaseExportOutline.vue'
import IconEdit from 'vue-material-design-icons/DatabaseEditOutline.vue'
import IconDelete from 'vue-material-design-icons/DatabaseRemoveOutline.vue'
import IconSave from 'vue-material-design-icons/Check.vue'
import IconCancel from 'vue-material-design-icons/Close.vue'
import IconActions from 'vue-material-design-icons/DatabaseCogOutline.vue'

export default {
	name: 'AdminSettings',

	components: {
		NcActions,
		NcActionButton,
		NcButton,
		NcCheckboxRadioSwitch,
		NcModal,
		NcSelect,
		IconApp,
		IconAdd,
		IconEdit,
		IconDelete,
		IconSave,
		IconCancel,
		IconActions,
	},

	props: [],

	data() {
		return {
			dialogServiceSettings: false,
			availableUsers: [],
			availableTypes: [],
			availableCollections: [],
			availableFormats: [],
			configuredSettings: [],
			configuredServices: [],
			selectedId: '',
			selectedUser: '',
			selectedServiceId: '',
			selectedServiceToken: '',
			selectedServiceName: '',
			selectedDataType: '',
			selectedDataCollection: '',
			selectedFormat: '',
			selectedServiceRestrictIP: '',
			selectedServiceRestrictMAC: '',
		}
	},

	computed: {
		userList() {
			return this.availableUsers.map(function(item) {
				if (item.name == null || (typeof item.name === 'string' && item.name.trim().length === 0)) {
					return { id: item.id, label: item.id }
				} else {
					return { id: item.id, label: item.name }
				}
			})
		},
		permissionsUserCreate: {
			get() {
				return (this.configuredSettings.permissions_user_create === '1')
			},
			set(value) {
				this.configuredSettings.permissions_user_create = (value === true) ? '1' : '0'
			},
		},
		permissionsUserModify: {
			get() {
				return (this.configuredSettings.permissions_user_modify === '1')
			},
			set(value) {
				this.configuredSettings.permissions_user_modify = (value === true) ? '1' : '0'
			},
		},
		permissionsUserDelete: {
			get() {
				return (this.configuredSettings.permissions_user_delete === '1')
			},
			set(value) {
				this.configuredSettings.permissions_user_delete = (value === true) ? '1' : '0'
			},
		},
	},

	watch: {
	},

	mounted() {
		this.loadData()
	},

	methods: {
		loadData() {
			this.fetchSettings()
			this.listUsers()
			this.listServices()
		},
		onAddClick() {
			this.clearSelected()
			this.dialogServiceSettings = true
		},
		onEditClick(id) {
			// clear values
			this.clearSelected()
			// find item
			const item = this.configuredServices.find(i => String(i.id) === String(id))
			// assign values
			this.selectedId = item.id
			this.selectedServiceId = item.service_id
			this.selectedServiceToken = item.service_token
			this.selectedServiceName = item.service_name
			this.selectedDataType = item.data_type
			this.selectedDataCollection = parseInt(item.data_collection)
			this.selectedFormat = item.format
			this.selectedServiceRestrictIP = item.restrict_ip
			this.selectedServiceRestrictMAC = item.restrict_mac
			// retrieve lists
			this.listTypes()
			this.listCollections()
			this.listFormats()
			// show settings dialog
			this.dialogServiceSettings = true
		},
		onDeleteClick(id) {
			// collect data
			const data = {
				id,
			}
			// execute delete command
			this.deleteService(data)
		},
		onSaveClick() {
			// collect data
			const data = {
				id: this.selectedId,
				uid: this.selectedUser,
				service_id: this.selectedServiceId,
				service_token: this.selectedServiceToken,
				service_name: this.selectedServiceName,
				data_type: this.selectedDataType,
				data_collection: this.selectedDataCollection,
				format: this.selectedFormat,
				restrict_ip: this.selectedRestrictIP,
				restrict_mac: this.selectedRestrictMAC,
			}
			// execute save
			if (this.selectedId.length !== 0) {
				this.modifyService(data)
			} else {
				this.createService(data)
			}
			// clear values
			this.clearSelected()
			// hide settings dialog
			this.dialogServiceSettings = false
		},
		onCancelClick() {
			this.clearSelected()
			this.dialogServiceSettings = false
		},
		onUserChanged() {
			this.listTypes()
			this.selectedDataType = ''
			this.selectedDataCollection = ''
			this.selectedFormat = ''
		},
		onDataTypeChanged() {
			this.listCollections()
			this.listFormats()
			this.selectedDataCollection = ''
			this.selectedFormat = ''
		},
		clearSelected() {
			this.selectedId = ''
			this.selectedUser = ''
			this.selectedServiceId = ''
			this.selectedServiceToken = ''
			this.selectedServiceName = ''
			this.selectedDataType = ''
			this.selectedDataCollection = ''
			this.selectedFormat = ''
			this.selectedServiceRestrictIP = ''
			this.selectedServiceRestrictMAC = ''
		},
		fetchSettings() {
			const uri = generateUrl('/apps/data/fetch-system-settings')
			axios.get(uri)
				.then((response) => {
					if (response.data) {
						this.configuredSettings = response.data
					}
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to retrieve settings')
						+ ': ' + error.response.request.responseText
					)
				})
				.then(() => {
				})
		},
		depositSettings() {
			const data = {
				data: this.configuredSettings,
			}
			const uri = generateUrl('/apps/data/deposit-system-settings')
			axios.put(uri, data)
				.then((response) => {
					showSuccess(t('data_service', 'Saved settings'))
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to save settings')
						+ ': ' + error.response.request.responseText
					)
				})
				.then(() => {
				})
		},
		listUsers() {
			const uri = generateUrl('/apps/data/list-users')
			axios.get(uri)
				.then((response) => {
					if (response.data) {
						this.availableUsers = response.data
					}
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to retrieve types')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		listTypes() {
			const uri = generateUrl('/apps/data/list-types')
			const data = {
				params: {
					user: this.selectedUser,
				},
			}
			axios.get(uri, data)
				.then((response) => {
					if (response.data) {
						this.availableTypes = response.data
					}
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to retrieve types')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		listCollections() {
			const uri = generateUrl('/apps/data/list-collections')
			const data = {
				params: {
					type: this.selectedDataType,
					user: this.selectedUser,
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
						t('data_service', 'Failed to retrieve collections')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		listFormats() {
			const uri = generateUrl('/apps/data/list-formats')
			const data = {
				params: {
					type: this.selectedDataType,
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
						t('data_service', 'Failed to retrieve formats')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		listServices() {
			const uri = generateUrl('/apps/data/list-services')
			const data = {
				params: {
					flagAdmin: true,
				},
			}
			axios.get(uri, data)
				.then((response) => {
					if (response.data) {
						this.configuredServices = response.data
					}
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to retrieve services')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		createService(data) {
			const uri = generateUrl('/apps/data/create-service')
			axios.put(uri, { data })
				.then((response) => {
					// show message
					showSuccess(t('data_service', 'Successfully created service'))
					// refresh services list
					this.listServices()
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to create service')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		modifyService(data) {
			const uri = generateUrl('/apps/data/modify-service')
			axios.put(uri, { data })
				.then((response) => {
					// show message
					showSuccess(t('data_service', 'Successfully modified service'))
					// refresh services list
					this.listServices()
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to modify service')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		deleteService(data) {
			const uri = generateUrl('/apps/data/delete-service')
			axios.put(uri, { data })
				.then((response) => {
					// show message
					showSuccess(t('data_service', 'Successfully deleted service'))
					// refresh services list
					this.listServices()
				})
				.catch((error) => {
					showError(
						t('data_service', 'Failed to delete service')
						+ ': ' + error.response?.request?.responseText
					)
				})
				.then(() => {})
		},
		formatDate(dt) {
			if (dt) {
				return (new Date(dt * 1000)).toLocaleString()
			} else {
				return 'never'
			}
		},
	},
}
</script>

<style scoped>
#ds_settings {
	.data-settings-page-title {
		display: flex;
		vertical-align: middle;
	}
	.data-settings-page-title h2 {
		padding-left: 1%;
	}
	.data-settings-section-services {
		padding-top: 2%;
	}
	.data-settings-section-actions{
		padding-top: 2%;
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
		width: 100%;
	}
	.data-settings-content-list table thead {
		backdrop-filter: brightness(1.5);
	}
	.data-settings-content-list table thead tr th{
		width: auto;
		padding-left: 10px;
		padding-right: 10px;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.data-settings-content-list table tbody tr td{
		width: auto;
		padding-left: 10px;
		padding-right: 10px;
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
