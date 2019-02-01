import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import find from "lodash/find";

class ApartmentType extends React.PureComponent {
    render() {
        const { apartment_types, id } = this.props;
        const type = find(apartment_types, {id});

        if (!type) return null;

        return (
            <span>{type.title}</span>
        );
    }
}

ApartmentType.propTypes = {
    /** Type ID */
    id: PropTypes.number.isRequired,
    /** Available types array */
    apartment_types: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            title: PropTypes.string.isRequired,
        })
    ),
};

const mapStateToProps = state =>
    ({
        apartment_types: state.abode.apartment_type.items,
    });

export default connect(mapStateToProps)(ApartmentType);