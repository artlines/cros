import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import {withTheme} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import Sidebar from './Sidebar';
import Header from './Header';

class Layout extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sidebarOpen: false,
        };
    }

    toggleSidebar = () => {
        this.setState({sidebarOpen: !this.state.sidebarOpen});
    };

    render() {
        const { theme, user: {roles}, children } = this.props;

        return (
            <div>
                <CssBaseline/>
                <Header handleClickMenu={this.toggleSidebar}/>
                <Sidebar
                    open={this.state.sidebarOpen}
                    onClose={this.toggleSidebar}
                    roles={roles}
                />
                <div style={{
                    padding: theme.spacing.unit * 3,
                }}>
                    {children}
                </div>
            </div>
        );
    }
}

const mapStateToProps = state =>
    ({
        user: state.system.user,
    });

export default compose(
    connect(mapStateToProps),
    withTheme(),
)(Layout);