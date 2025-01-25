/**
 * Newspack Tecnavia Integration Access Settings  toggle JS.
 *
 * Adds script to handle the toggling of dependency-dependent elements.
 * Basically, based on the state of a dependency checkbox element
 * it will enable or disable the dependent elements.
 *
 * @link   https://www.newspack.com
 * @file   This files defines the JS script to handle the toggling of dependent elements.
 * @author Automattic
 * @since  1.0
 */

/**
 * Toggles the dependent elements based on the state of the dependency element.
 *
 * @param {HTMLElement} dependencyElementCheckbox The dependency element.
 * @param {NodeList} dependentElements The dependent elements.
 *
 * @return void
 */
function toggleDependentElements ( dependencyElementCheckbox, dependentElements ) {
	// Loop through the dependent elements.
	dependentElements.forEach( ( element ) => {
		// Get the dependency element on which the element is dependent.
		const dependencyElement = element.getAttribute("data-dependent-on");

		// Check only for the elements that are dependent on the dependencyElement.
		if ( dependencyElement !== dependencyElementCheckbox.id ) {
			// Skip this iteration.
			return;
		}

		// If the checkbox is checked, disable the dependent elements.
		if ( ! dependencyElementCheckbox.checked ) {
			element.removeAttribute("disabled");
		} else {
			element.setAttribute("disabled", true);
		}
	});
};

/**
 * Initialize the script.
 */
function init() {
	// Get the checkbox element.
	const dependencyElementCheckbox = document.querySelector(
		'input[data-dependency="1"]'
	);

	// Elements that have 'data-dependent-on' attribute.
	const dependentElements = document.querySelectorAll(
		'[data-dependent-on]'
	);

	// Check if the elements exist.
	if ( ! dependencyElementCheckbox || dependentElements.length === 0 ) {
		console.warn( "Newspack Tecnavia integration : Dependency checkbox or dependent elements not found." );
		return;
	}

	// Run the function on page load.
	toggleDependentElements( dependencyElementCheckbox, dependentElements );

	// Toggle on change.
	dependencyElementCheckbox.addEventListener( "change", () => { toggleDependentElements( dependencyElementCheckbox, dependentElements ); } );
}

// Run the script on DOMContentLoaded.
document.addEventListener( 'DOMContentLoaded', init );
