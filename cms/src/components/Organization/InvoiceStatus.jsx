import React from "react";
import PropTypes from "prop-types";

const statuses = {
    1: "Не оплачен",
    2: "Частично оплачен",
    3: "Оплачен",
};

class InvoiceStatus extends React.PureComponent {
    render() {
        const { id } = this.props;

        if (!statuses[id]) return null;

        return (
            <span>{statuses[id]}</span>
        );
    }
}

InvoiceStatus.propTypes = {
    /** Status ID */
    id: PropTypes.number.isRequired,
};

export default InvoiceStatus;