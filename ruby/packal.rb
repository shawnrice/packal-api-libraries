class Packal
	def self.queue(json)
		begin
			Packal.send(JSON.parse(json))
		rescue StandardError => e
			e.message
		end
	end

	def self.submit(params)
		params["content_type"] = :json
		type = ('workflow_revision' == params.keys[0].to_s) ? 'workflow'.to_sym : params.keys[0]
		RestClient.post( "http://localhost:3000/api/v1/alfred2/#{type}/submit", params)
	end

	def self.send(params)
		key = params.keys[0]
		['username', 'password'].each do |k|
			unless (params.key? k) then
				raise StandardError.new("Error: you need to pass a #{k} to submit a #{key}.")
			end
		end
		Packal::const_get(key.capitalize).submit(params)
	end

	class Report
		def self.submit(params)
			Packal.ensure_keys(params, ['workflow_revision_id', 'report_type', 'message'])
			Packal.submit(params)
		end
	end
	class Theme
		def self.submit(params)
			#params['theme']['alfred2'] = true
			Packal.ensure_keys(params, ['name', 'description', 'uri'])
			Packal.submit(params)
		end
	end

	class Workflow
		def self.submit(params)
			Packal.ensure_keys(params, ['file'])
			file = ensure_file(params)
			check_mime_type(file)
			data = encode_file(file)
			Packal.submit(fix_keys(params, data))
		end
		private
			def self.ensure_file(params)
				file = params['workflow']['file']
				if ! File.exist?(file) then
					raise StandardError.new("Error: file #{file} not found.")
				end
				file
			end
			def self.check_mime_type(file)
				unless ('zip' == `file --mime -b #{file}`.split("/")[1].split(';')[0])
					raise StandardError.new('Error: workflow file is not a valid archive.')
				end
			end
			def self.encode_file(file)
				'data:application/zip;base64,' + Base64.encode64(open(file) { |io| io.read })
			end
			def self.fix_keys(params, data)
				params['workflow_revision'] = { :file => data }
				params.delete('workflow')
				keys = params.keys.reverse.map(&:to_sym)
				params = params.to_a.reverse.map{|x| x[1]}
				Hash[keys.zip(params)]
			end
	end

	private
		def self.ensure_keys(params, keys)
			key = params.keys[0]
			keys.each do |k|
				unless (params[key].key? k) then
					raise StandardError.new("Error: you need to pass a #{k} to submit a #{key}.")
				end
			end
			true
		end
end