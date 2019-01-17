import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import {withTheme} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import Sidebar from 'components/common/Sidebar';
import Header from 'components/common/Header';
import Routes from "./Routes";
import system from 'actions/system';

class App extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sidebarOpen: false,
        };
    }

    componentDidMount() {
        this.props.loadInitialData();
    }

    toggleSidebar = () => {
        this.setState({sidebarOpen: !this.state.sidebarOpen});
    };

    render() {
        const { theme, user: {error, isFetching, id, roles} } = this.props;

        if (!error && !isFetching && id) {
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
                        <Routes roles={roles}/>
                    </div>
                </div>
            );
        }

        return null;
    }
}

const mapStateToProps = state =>
    ({
        user: state.system.user,
    });

const mapDispatchToProps = dispatch =>
    ({
        loadInitialData: () => {
            dispatch(system.fetchMe());
        },
    });

export default compose(
    connect(mapStateToProps, mapDispatchToProps),
    withTheme(),
)(App);