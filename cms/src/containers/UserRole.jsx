import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import find from 'lodash/find';

class UserRole extends React.PureComponent {
    render() {
        const { roles: { items }, role } = this.props;
        const roleRes = find(items, {key: role});

        return (
            <React.Fragment>
                {roleRes ? roleRes.title : 'Нет роли'}
            </React.Fragment>
        );
    }
}

UserRole.propTypes = {
    /**
     * User role
     */
    role: PropTypes.string.isRequired,

    roles: PropTypes.object.isRequired,
};

const mapStateToProps = state =>
    ({
        roles: state.system.roles,
    });

export default connect(mapStateToProps)(UserRole);