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
	<div>
		<NcButton @click="openServiceSettings()">
			Show Settings
		</NcButton>

		<NcAppSettingsDialog :open.sync="ServiceSettingsDialog" name="Application settings">
			<NcAppSettingsSection id="asci-name-1" name="Example name 1">
				Some example content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-2" name="Example name 2">
				Some more content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-3" name="Example name 3">
				Some example content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-4" name="Example name 4">
				Some more content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-5" name="Example name 5">
				Some example content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-6" name="Example name 6">
				Some more content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-7" name="Example name 7">
				Some example content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-8" name="Example name 8">
				Some more content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-9" name="Example name 9">
				Some more content
			</NcAppSettingsSection>
			<NcAppSettingsSection id="asci-name-10" name="Example name 10">
				Some more content
			</NcAppSettingsSection>
		</NcAppSettingsDialog>
	</div>
</template>

<script>
import NcAppSettingsDialog from '@nextcloud/vue/dist/Components/NcAppSettingsDialog.js'
import NcAppSettingsSection from '@nextcloud/vue/dist/Components/NcAppSettingsSection.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

export default {
	name: 'UserSettings',

	components: {
		NcAppSettingsDialog,
		NcAppSettingsSection,
		NcButton,
	},

	props: [],

	data() {
		return {
			ServiceSettingsDialog: false,
		}
	},

	methods: {
		methods: {
			openServiceSettings() {
				this.ServiceSettingsDialog = true
			},
			closeServiceSettings() {
				this.ServiceSettingsDialog = false
			},
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
}
</style>
