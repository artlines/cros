import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import find from 'lodash/find';
import isString from 'lodash/isString';

class UserRole extends React.PureComponent {
    render() {
        const { roles: { items }, role } = this.props;
        const roleArr = isString(role) ? JSON.parse(role) : role;

        const roleRes = roleArr[0] ? find(items, {key: roleArr[0]}) : null;

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
    role: PropTypes.oneOfType([PropTypes.string, PropTypes.array]),

    roles: PropTypes.object.isRequired,
};

const mapStateToProps = state =>
    ({
        roles: state.system.roles,
    });

export default connect(mapStateToProps)(UserRole);