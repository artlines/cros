import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import find from "lodash/find";

class RoomType extends React.PureComponent {
    render() {
        const { room_types, id } = this.props;
        const type = find(room_types, {id});

        if (!type) return null;

        return (
            <span>{type.title}</span>
        );
    }
}

RoomType.propTypes = {
    /** Type ID */
    id: PropTypes.number.isRequired,
    /** Available types array */
    room_types: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            title: PropTypes.string.isRequired,
        })
    ),
};

const mapStateToProps = state =>
    ({
        room_types: state.abode.room_type.items,
    });

export default connect(mapStateToProps)(RoomType);