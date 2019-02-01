import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import find from "lodash/find";

class ParticipationClass extends React.PureComponent {
    render() {
        const { participation_classes, id } = this.props;
        const participation_class = find(participation_classes, {id});

        if (!participation_class) return null;

        return (
            <span>{participation_class.title}</span>
        );
    }
}

ParticipationClass.propTypes = {
    /** Class ID */
    id: PropTypes.number.isRequired,
    /** Available types array */
    participation_classes: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            title: PropTypes.string.isRequired,
        })
    ),
};

const mapStateToProps = state =>
    ({
        participation_classes: state.abode.participation_class.items,
    });

export default connect(mapStateToProps)(ParticipationClass);