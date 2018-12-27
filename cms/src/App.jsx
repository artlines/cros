import React from 'react';
import AppBar from '@material-ui/core/AppBar';
import IconButton from '@material-ui/core/IconButton';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import MenuIcon from '@material-ui/icons/Menu';
import CssBaseline from '@material-ui/core/CssBaseline';
import Sidebar from 'components/common/Sidebar';

class App extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sidebarOpen: false,
        };
    }

    toggleSidebar = () => this.setState({sidebarOpen: !this.state.sidebarOpen});

    render() {


        return (
            <div>
                <CssBaseline/>
                <AppBar color={`default`} position={`static`}>
                    <Toolbar>
                        <IconButton onClick={this.toggleSidebar}>
                            <MenuIcon/>
                        </IconButton>
                        <Typography variant={`h6`} noWrap color={`inherit`}>
                            CROS
                        </Typography>
                    </Toolbar>
                </AppBar>
                <Sidebar open={this.state.sidebarOpen} onClose={this.toggleSidebar}/>
            </div>
        );
    }
}

export default App;