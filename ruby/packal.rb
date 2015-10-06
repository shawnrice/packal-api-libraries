require 'rest-client'
require 'json'


# The Packal class to submit to the Packal API.
#
# Example Use:
#
# Submit a Workflow:
# =================
# json = {
# 	:workflow => {:file => '/path/to/workflow/file/workflow.alfredworkflow', :version => '1.0.0'},
# 	:username => "USERNAME",
# 	:password => "PASSWORD"
# }
# output = Packal.queue(json.to_json)
#
# Submit a Theme:
# ==============
# json = {
# 	:theme => {
# 		:name => 'Testing, one, two, three',
# 		:description => 'This is a description',
# 		:uri => 'alfred://theme/....',
# 		:tags => ['tag_one', 'tag_2']
# 		},
# 	:username => "USERNAME",
# 	:password => "PASSWORD"
# }
# output = Packal.queue(json.to_json)
#
# Submit a Report:
# ===============
# json = {
# 	:report => {
# 		:type => 'Malicious Code',
# 		:workflow_revision_id => 534,
# 		:message => 'There is some bad code in this thing, yo.'
# 		},
# 	:username => "USERNAME",
# 	:password => "PASSWORD"
# }
# output = Packal.queue(json.to_json)
#
class Packal
	def self.queue(json)
		begin
			# Start everythng up and parse the JSON first
			Packal.send(JSON.parse(json))
		rescue StandardError => e
			# If there is an error, then print the error
			e.message
		end
	end

	# Actually submit the data to Packal
	def self.submit(params)
		type = ('workflow_revision' == params.keys[0].to_s) ? 'workflow'.to_sym : params.keys[0]

		# Development Server
		# RestClient::Request.execute(:url => "http://localhost:3000/api/v1/alfred2/#{type}/submit", :payload => params, :method => :post) # development

		# Staging Server
		RestClient::Request.execute(:url => "https://mellifluously.org/api/v1/alfred2/#{type}/submit", :payload => params, :method => :post)

		# Production Server
		# RestClient::Request.execute(:url => "https://www.packal.org/api/v1/alfred2/#{type}/submit", :payload => params, :method => :post)
	end

	def self.send(params)
		# This is the type that is being sent through the metaclass
		type = params.keys[0]
		# Check to make sure that we are sending a USERNAME and a PASSWORD
		['username', 'password'].each do |k|
			unless (params.type? k) then
				raise StandardError.new("Error: you need to pass a #{k} to submit a #{type}.")
			end
		end

		# So, we'll make sure we use the correct submission process by using the submit method
		# of the appropriate type class. See below.
		Packal::const_get(type.capitalize).submit(params)
	end

	# Class to submit a report to Packal.org
	class Report
		def self.submit(params)
			# Make sure that we have the keys necessary for the report
			Packal.ensure_keys(params, ['workflow_revision_id', 'report_type', 'message'])
			# Submit through the Packal class
			Packal.submit(params)
		end
	end

	# Class to submit a theme to Packal.org
	class Theme
		def self.submit(params)
			# Make sure that we have all the keys necessary for the theme
			Packal.ensure_keys(params, ['name', 'description', 'uri'])
			# Submit through the Packal class
			Packal.submit(params)
		end
	end

	# Class to submit a Workflow to Packal.org
	class Workflow
		def self.submit(params)
			# Make sure that we have the "file" and "version" keys set
			Packal.ensure_keys(params, ['file', 'version'])
			# Make sure that the file exists
			file = ensure_file(params)
			# Make sure that the file has a zip mime-type
			check_mime_type(file)
			# Open the file as a resource so that we send the data
			data = File.new(file, 'rb')
			# Submit the workflow
			Packal.submit(fix_keys(params, data))
		end

		# Private Methods for the Workflow Class
		private

			# Ensures that the file to upload exists
			def self.ensure_file(params)
				file = params['workflow']['file']
				if ! File.exist?(file) then
					raise StandardError.new("Error: file #{file} not found.")
				end
				file
			end

			# Checks to make sure that the mimetype of the file is a valid archive
			def self.check_mime_type(file)
				unless ('zip' == `file --mime -b "#{file}"`.split("/")[1].split(';')[0])
					raise StandardError.new('Error: workflow file is not a valid archive.')
				end
			end

			# Fixes the keys so that they work correctly
			def self.fix_keys(params, data)
				# The problem is that the person using this library wants to submit a "workflow" but the
				# data on Packal.org is actually a workflow_revision, and so for the params to work correctly
				# we will just have to reshuffle the parameters.

				# Add the workflow_revision parameter
				params['workflow_revision'] = { :file => data, :version => params['workflow']['version'] }
				# Delete the workflow parameter
				params.delete('workflow')
				# Shuffle the keys a bit to make them play nice with the rest of the class because 'workflow_revision'
				# needs to be the first index
				keys = params.keys.reverse.map(&:to_sym)
				params = params.to_a.reverse.map{|x| x[1]}
				# Send the fixed parameters
				Hash[keys.zip(params)]
			end
	end

	# Private methods for the Packal class
	private
		# This method makes sure that the keys are present in the params Hash
		def self.ensure_keys(params, keys)
			# The first key at index '0' will be the 'type'
			type = params.keys[0]
			keys.each do |k|
				unless (params[type].key? k) then
					raise StandardError.new("Error: you need to pass a #{k} to submit a #{type}.")
				end
			end
			true
		end
end