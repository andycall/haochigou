define(function(){

	function Timer(tickCallback){
		this.time = 0;
		this.tickCallback = tickCallback;
	}


	Timer.prototype.start = function(interval){
		var self = this;
		var startTime = (new Date()).getTime();
		self.time = interval;

		self.times = setInterval(function(){

			var date = new Date(),
				now = date.getTime(),
				differences = interval -  parseInt((now - startTime) / 1000);

			self.time = differences;

			self.tickCallback(differences);

			self.time_by_second(self.breakpoint, self.breakCallback);

			if(differences < 0){
				self.stop();
			}

		}, 998);
	};

	Timer.prototype.stop = function(){
		var self = this;
		clearTimeout(self.times);
		self.times = null;
	};

	Timer.prototype.restart = function(){
		var self = this;
		self.stop();
		self.start();
	};

	Timer.prototype.time_by_second = function(breakpoint, callback){
		var self = this;
		self.breakpoint = breakpoint;
		self.breakCallback = callback;

		if(self.time < breakpoint){
			callback();
		}
	};

	return Timer;
});