<?php
/**
 * Similar listings settings template for the listing type editor.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div v-if="currentSubTab == 'similar-listings'" class="tab-content align-center">
	<div class="form-section mb40">
		<h3>Enable Similar listings</h3>
		<p>
			You can optionally display a list of similar listings in the single listing page.
			This section will appear at the end of the page, below the current listing information.
		</p>
		<div class="form-group">
			<label class="form-switch">
				<input type="checkbox" v-model="single.similar_listings.enabled">
				<span class="switch-slider"></span>
			</label>
		</div>
	</div>

	<div :class="!single.similar_listings.enabled?'ml-overlay-disabled':''">
		<div class="form-section mb40">
			<h4 class="mb10">Matching similar listings</h4>
			<p>Determine what should classify as a similar listing to the currently active one, based on the following attributes.</p>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_type" class="form-checkbox">
					Must belong to the same listing type.
				</label>
			</div>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_category" class="form-checkbox">
					Must have at least one category in common.
				</label>
			</div>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_tags" class="form-checkbox">
					Must have at least one tag in common.
				</label>
			</div>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_region" class="form-checkbox">
					Must belong to the same region (Regions taxonomy).
				</label>
			</div>
			
			<div v-for="taxonomy in editor.taxonomies_27">
				<div class="form-group mb10">
					<label>
						<input type="checkbox" v-model="single.similar_listings[taxonomy.type]" class="form-checkbox">
						Must have at least one "{{ taxonomy.label }}" item in common.
					</label>
				</div>
			</div>
		</div>
			


		<div class="form-section mb40">
			<h4 class="mb10">Displaying similar listings</h4>
			<div class="form-group">
				<label>Order listings by</label>
				<div class="select-wrapper" style="display: inline-block; width: 160px;">
					<select v-model="single.similar_listings.orderby">
						<option value="priority">Priority</option>
						<option value="rating">Rating</option>
						<option value="proximity">Proximity</option>
						<option value="random">Random</option>
					</select>
				</div>
			</div>

			<div class="form-group" v-show="single.similar_listings.orderby === 'proximity'">
				<br>
				<label>Listing must be within radius (in kilometers)</label>
				<input type="number" v-model="single.similar_listings.max_proximity" style="display: inline-block; width: 100px;">
			</div>

			<div class="form-group">
				<br>
				<label>Number of listings to show</label>
				<input type="number" v-model="single.similar_listings.listing_count" style="display: inline-block; width: 100px;">
			</div>


			<div class="form-group">
				<br>
				<label class="mb10">Layout</label>
				<div class="select-wrapper" style="display: inline-block; width: 160px;">
					<select v-model="single.similar_listings.layout">
						<option value="grid">Grid</option>
						<option value="isotope">Masonry</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<br>
				<label>Enable package visibility?</label>
				<label class="form-switch">
					<input type="checkbox" v-model="single.similar_listings.enable_package_visibility">
					<span class="switch-slider"></span>
				</label>
			</div>
			<div class="form-group" v-if="single.similar_listings.enable_package_visibility">
				<br>
				<label>Display Similar Listings for Package IDs:</label>
				<p>Enter package IDs separated by a comma (e.g. 167, 168, 169)</p>
				<input type="text" v-model="single.similar_listings.listing_packages" style="display: inline-block;">
			</div>
		</div>
	</div>
</div>