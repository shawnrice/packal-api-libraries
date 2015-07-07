require_relative 'packal'

def main(file)
	json = {
		:workflow => { :file => file },
		:username => "Username",
		:password => "Password"
	}
	output = Packal.queue(json.to_json)

	begin
		JSON.parse(output)
	rescue
		output
	end
end

def check_valid_file(file)
	unless File.exist?(file) then
		raise StandardError.new("Error: #{file} does not exist.")
	end
	unless ('zip' == `file --mime -b #{file}`.split("/")[1].split(';')[0]) then
		raise StandardError.new('Error: workflow file is not a valid archive.')
	end
	unless 'alfredworkflow' != File.extname(file) then
		raise StandardError.new('Error: workflow file is does not have an alfredworkflow extension.')
	end
	true
end

if (0 === ARGV.count) then
	abort("Error: you need to pass a path to an alfredworkflow file as an argument.")
end

# File is a file path, like '/path/to/workflow.alfredworkflow'
if check_valid_file(ARGV[0]) then
	pp main(file)
end