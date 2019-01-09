import AbstractRestModuleClass from './abstract-rest-module-class';

class PlayersModule extends AbstractRestModuleClass {
    modelUrl = 'players';

    constructor() {
        super();
    }
}

export default PlayersModule;
