import React from 'react';
import {connect} from 'react-redux';
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
        const { user } = this.props;

        if (!user) {
            return null;
        }

        return (
            <div>
                <CssBaseline/>
                <Header handleClickMenu={this.toggleSidebar}/>
                <Sidebar open={this.state.sidebarOpen} onClose={this.toggleSidebar}/>
                <Routes/>
            </div>
        );
    }
}

const mapStateToProps = state =>
    ({
        user: state.system.user,
    });

const mapDispatchToProps = dispatch =>
    ({
        loadInitialData: () => {
            dispatch(system.fetchUser());
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(App);